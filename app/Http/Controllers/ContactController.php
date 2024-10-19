<?php

namespace App\Http\Controllers;

use App\Contracts\ContactRepositoryInterface;
use App\DTO\ContactDTO;
use App\Http\Requests\ContactRequest;
use App\Http\Resources\ContactResource;
use App\Services\ViaCepService;
use Illuminate\Http\{Request, Response};
use Illuminate\Support\Facades\{DB, Log};

class ContactController extends Controller
{
    protected $contactRepository;

    protected $viaCepService;

    public function __construct(ContactRepositoryInterface $contactRepository, ViaCepService $viaCepService)
    {
        $this->contactRepository = $contactRepository;
        $this->viaCepService     = $viaCepService;
    }

    public function index()
    {
        $contacts = $this->contactRepository->getAllContacts();

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

        $contacts = $this->contactRepository->searchContacts($searchTerm);

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
            $contact = $this->contactRepository->getContactById($id);

            return ContactResource::make($contact);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Contato não encontrado.',
            ], Response::HTTP_NOT_FOUND);
        }
    }

    public function store(ContactRequest $request)
    {
        DB::beginTransaction();

        try {
            $cep         = $request->input('cep');
            $addressData = $this->viaCepService->getAddressByCep($cep);

            $contactDTO = new ContactDTO(
                $request->input('name'),
                $request->input('phone'),
                $request->input('email'),
                $request->input('number'),
                $cep,
                $addressData['logradouro'] . ', ' . $addressData['bairro'] . ', ' . $addressData['localidade'] . ' - ' . $addressData['uf']
            );

            $contact = $this->contactRepository->createContact([
                'name'    => $contactDTO->name,
                'phone'   => $contactDTO->phone,
                'email'   => $contactDTO->email,
                'number'  => $contactDTO->number,
                'cep'     => $contactDTO->cep,
                'address' => $contactDTO->address,
            ]);

            DB::commit();

            return (ContactResource::make($contact))
                ->response()
                ->setStatusCode(Response::HTTP_CREATED);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('contact')->error('Erro ao criar contato: ' . $e->getMessage(), [
                'code'       => $e->getCode(),
                'line'       => $e->getLine(),
                'file'       => $e->getFile(),
                'trace'      => $e->getTraceAsString(),
                'data_input' => $request->all(),
            ]);

            return response()->json([
                'message' => 'Ocorreu um erro ao tentar criar o contato.',
                'details' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(ContactRequest $request, $id)
    {
        DB::beginTransaction();

        try {
            if ($request->has('cep')) {
                $cep         = $request->input('cep');
                $addressData = $this->viaCepService->getAddressByCep($cep);
                $address     = $addressData['logradouro'] . ', ' . $addressData['bairro'] . ', ' . $addressData['localidade'] . ' - ' . $addressData['uf'];
                $request->merge(['address' => $address]);
            }

            $this->contactRepository->updateContact($id, $request->all());

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

    public function destroy($id)
    {
        try {
            $this->contactRepository->deleteContact($id);

            return response()->json([
                'message' => 'Contato excluído com sucesso.',
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao excluir contato.',
                'details' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}
