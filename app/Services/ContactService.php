<?php

namespace App\Services;

use App\DTO\ContactDTO;
use App\Integrations\ViaCepIntegration;
use App\Repositories\ContactRepository;
use App\ValueObjects\Address;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ContactService
{
    public function __construct(private ViaCepIntegration $viaCep, private ContactRepository $contactRepository)
    {
        $this->viaCep            = $viaCep;
        $this->contactRepository = $contactRepository;
    }

    public function listContacts()
    {
        Log::info('===================== Listando todos os contatos =====================');

        return $this->contactRepository->paginateContacts();
    }

    public function searchContactsByCriteria(array $searchTerm)
    {
        Log::info('===================== Buscando contatos com critérios: ===================== ', $searchTerm);

        if (empty(array_filter($searchTerm))) {
            Log::warning('Tentativa de busca sem critérios fornecidos.');

            throw new \Exception("Por favor, forneça pelo menos um termo de pesquisa.");
        }

        $contacts = $this->contactRepository->findContactsByFilters($searchTerm);

        if ($contacts->isEmpty()) {
            Log::warning('Nenhum contato encontrado para os critérios fornecidos.', $searchTerm);

            throw new \Exception("Nenhum contato encontrado para o termo fornecido.");
        }

        return $contacts;
    }

    public function retrieveContact($id)
    {
        Log::info("===================== Recuperando contato ID: {$id} =====================");

        $contact = $this->contactRepository->findContactById($id);

        if (empty($contact)) {
            Log::error("Contato não encontrado para ID: {$id}");

            throw new \Exception("Contato não encontrado.");
        }

        return $contact;
    }

    public function registerNewContact(array $data)
    {
        Log::info('===================== Iniciando registro de novo contato =====================', $data);

        if (!isset($data['name'], $data['phone'], $data['email'], $data['number'], $data['cep'])) {
            Log::error('Dados incompletos ao tentar registrar um contato', $data);

            throw ValidationException::withMessages(['error' => 'Todos os campos são obrigatórios.']);
        }

        $cep         = $data['cep'];
        $addressData = $this->viaCep->getAddressByCep($cep);

        if (!$addressData || !isset($addressData->logradouro, $addressData->bairro, $addressData->localidade, $addressData->uf)) {
            Log::error("Endereço não encontrado para o CEP: {$cep}");

            throw ValidationException::withMessages(['cep' => 'Endereço não encontrado para o CEP fornecido.']);
        }

        $contactDTO = new ContactDTO(
            $data['name'],
            $data['phone'],
            $data['email'],
            $data['number'],
            $cep,
            Address::fromArray([
                'logradouro' => $addressData->logradouro ?? '',
                'complemento' => $addressData->complemento ?? '',
                'unidade'     => $addressData->unidade ?? '',
                'bairro'     => $addressData->bairro ?? '',
                'localidade' => $addressData->localidade ?? '',
                'uf'         => $addressData->uf ?? '',
                'estado'     => $addressData->uf ?? '',
                'regiao'     => $addressData->regiao ?? '',
                'ibge'       => $addressData->ibge ?? null,
                'gia'        => $addressData->gia ?? null,
                'ddd'        => $addressData->ddd ?? null,
                'siafi'      => $addressData->siafi ?? null,
            ]),
        );

        try {
            $contact = $this->contactRepository->insertContact($contactDTO);
            Log::info("Novo contato registrado com sucesso! ID: {$contact->id}");

            return $contact;
        } catch (\Exception $e) {
            Log::error("Erro ao inserir contato: " . $e->getMessage(), ['exception' => $e]);

            throw new \RuntimeException("Erro ao inserir o contato: " . $e->getMessage());
        }
    }

    public function editContactDetails($id, array $data)
    {
        Log::info("===================== Editando contato ID: {$id} =====================", $data);

        $contact = $this->contactRepository->findContactById($id);

        if (!$contact) {
            Log::error("Tentativa de edição falhou. Contato não encontrado para ID: {$id}");

            throw new \Exception("Contato não encontrado.");
        }

        if (isset($data['cep'])) {
            $cep         = $data['cep'];
            $addressData = $this->viaCep->getAddressByCep($cep);

            if (!$addressData) {
                Log::error("Endereço não encontrado para CEP: {$cep} ao editar contato ID: {$id}");

                throw ValidationException::withMessages(['cep' => 'Endereço não encontrado para o CEP fornecido.']);
            }

            $data['address'] = Address::fromArray([
                'logradouro' => $addressData->logradouro ?? '',
                'complemento' => $addressData->complemento ?? '',
                'unidade' => $addressData->unidade ?? '',
                'bairro'     => $addressData->bairro ?? '',
                'localidade' => $addressData->localidade ?? '',
                'uf'         => $addressData->uf ?? '',
                'estado'     => $addressData->uf ?? '',
                'regiao'     => $addressData->regiao ?? '',
                'ibge'       => $addressData->ibge ?? null,
                'gia'        => $addressData->gia ?? null,
                'ddd'        => $addressData->ddd ?? null,
                'siafi'      => $addressData->siafi ?? null,
            ]);
        }

        try {
            $this->contactRepository->modifyContact($id, $data);
            Log::info("Contato ID: {$id} editado com sucesso.");

            return $contact;
        } catch (\Exception $e) {
            Log::error("Erro ao editar contato ID: {$id}: " . $e->getMessage(), ['exception' => $e]);

            throw new \RuntimeException("Erro ao editar o contato: " . $e->getMessage());
        }
    }

    public function removeContactById($id)
    {
        Log::info("===================== Removendo contato ID: {$id} =====================");

        try {
            $this->contactRepository->removeContact($id);
            Log::info("Contato ID: {$id} removido com sucesso.");

            return true;
        } catch (\Exception $e) {
            Log::error("Erro ao remover contato ID: {$id}: " . $e->getMessage(), ['exception' => $e]);

            throw new \RuntimeException("Erro ao remover o contato: " . $e->getMessage());
        }
    }
}
