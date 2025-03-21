<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use App\Http\Resources\ContactResource;
use App\Services\ContactService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\{JsonResponse, Request, Response};
use Illuminate\Support\Facades\{DB};

class ContactController extends Controller
{
    private $contactService;

    public function __construct(ContactService $contactService)
    {
        $this->contactService = $contactService;
    }

    public function index(): AnonymousResourceCollection
    {
        $contacts = $this->contactService->listContacts();

        return ContactResource::collection($contacts);
    }

    public function search(Request $request)
    {
        $searchTerm = $request->only(['name', 'email', 'cep', 'number']);

        if (empty(array_filter($searchTerm))) {
            return response()->json([
                'message' => 'Por favor, forneça pelo menos um termo de pesquisa.',
            ], Response::HTTP_BAD_REQUEST);
        }

        $contacts = $this->contactService->searchContactsByCriteria($searchTerm);

        if ($contacts->isEmpty()) {
            return response()->json([
                'message' => 'Nenhum contato encontrado para o termo fornecido.',
            ], Response::HTTP_NOT_FOUND);
        }

        return ContactResource::collection($contacts);
    }

    public function show($id)
    {
        try {
            $contact = $this->contactService->retrieveContact($id);

            return ContactResource::make($contact);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao buscar contato.',
                'details' => $e->getMessage(),
            ], $e->getCode() ?: Response::HTTP_NOT_FOUND);
        }
    }

    public function store(ContactRequest $request)
    {
        DB::beginTransaction();

        try {
            $contact = $this->contactService->registerNewContact($request->all());

            DB::commit();

            return response()->json([
                'message' => 'Contato registrado com sucesso.',
                'data'    => new ContactResource($contact),
            ], Response::HTTP_CREATED);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Erro ao registrar contato.',
                'details' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(ContactRequest $request, $id): JsonResponse
    {
        DB::beginTransaction();

        try {

            $this->contactService->editContactDetails($id, $request->all());

            DB::commit();

            return response()->json([
                'message' => 'Contato atualizado com sucesso.',
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Erro ao atualizar contato.',
                'details' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $this->contactService->removeContactById($id);

            return response()->json([
                'message' => 'Contato removido com sucesso.',
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao excluir contato.',
                'details' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}
