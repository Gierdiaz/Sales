<?php

namespace Tests\Feature;

use App\Models\{Contact, User};
use App\Services\ViaCepService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\{deleteJson, getJson, postJson, putJson};

uses(RefreshDatabase::class);

describe('Managing Contact Records', function () {
    beforeEach(function () {
        Sanctum::actingAs(User::factory()->create());
    });

    it('can list contacts', function () {
        Contact::factory()->count(3)->create();

        getJson(route('contacts.index'))
            ->assertStatus(200)
            ->assertJsonCount(3);
    });

    it('can show a contact', function () {
        $contact = Contact::factory()->create();

        getJson(route('contacts.show', ['id' => $contact->id]))
            ->assertStatus(200)
            ->assertJson([
                'data' => $contact->toArray() + ['links' => [
                    'index' => route('contacts.index'),
                ]],
            ]);
    });

    it('can store a contact', function () {
        $data = [
            'name'   => 'John Doe',
            'phone'  => '123456789',
            'email'  => 'john@example.com',
            'number' => '10',
            'cep'    => '01001-000',
        ];

        $mock = \Mockery::mock(ViaCepService::class);
        $mock->shouldReceive('getAddressByCep')->with($data['cep'])->andReturn([
            'logradouro' => 'Praça da Sé',
            'bairro'     => 'Sé',
            'localidade' => 'São Paulo',
            'uf'         => 'SP',
        ]);
        app()->instance(ViaCepService::class, $mock);

        postJson(route('contacts.store'), $data)
            ->assertStatus(201);

        $this->assertDatabaseHas('contacts', [
            'name'   => 'John Doe',
            'phone'  => '123456789',
            'email'  => 'john@example.com',
            'number' => '10',
            'cep'    => '01001-000',
        ]);
    });

    it('can update a contact', function () {
        $contact = Contact::factory()->create();

        $data = [
            'name'   => 'Jane Doe',
            'phone'  => '987654321',
            'email'  => 'jane@example.com',
            'number' => '20',
            'cep'    => '02002-000',
        ];

        $mock = \Mockery::mock(ViaCepService::class);
        $mock->shouldReceive('getAddressByCep')->with($data['cep'])->andReturn([
            'logradouro' => 'Rua da Luz',
            'bairro'     => 'Luz',
            'localidade' => 'São Paulo',
            'uf'         => 'SP',
        ]);
        app()->instance(ViaCepService::class, $mock);

        putJson(route('contacts.update', ['id' => $contact->id]), $data)
            ->assertStatus(200)
            ->assertJson(['message' => 'Contato atualizado com sucesso.']);

        $this->assertDatabaseHas('contacts', [
            'id'     => $contact->id,
            'name'   => 'Jane Doe',
            'phone'  => '987654321',
            'email'  => 'jane@example.com',
            'number' => '20',
            'cep'    => '02002-000',
        ]);
    });

    it('can delete a contact', function () {
        $contact = Contact::factory()->create();

        deleteJson(route('contacts.destroy', ['id' => $contact->id]))
            ->assertStatus(200);

        $this->assertSoftDeleted('contacts', ['id' => $contact->id]);

    });

});
