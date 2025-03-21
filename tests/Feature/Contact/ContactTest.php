<?php

namespace Tests\Feature;

use App\Integrations\ViaCepIntegration;
use App\Models\{Contact, User};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Mockery;

use function Pest\Laravel\{deleteJson, getJson, postJson, putJson};

uses(RefreshDatabase::class);

describe('Managing Contact Records', function () {
    beforeEach(function () {
        $this->user = User::factory()->create([
            'name'     => 'Gierdiaz',
            'email'    => 'gierdiaz@hotmail.com',
            'password' => bcrypt('password'),
        ]);

        Sanctum::actingAs($this->user);
    });

    it('can list contacts', function () {
        Contact::factory()->create([
            'name'   => 'Állison Luis',
            'phone'  => '21997651914',
            'number' => '43',
            'email'  => 'gierdiaz@hotmail.com',
            'cep'    => '23017-130',
        ]);

        getJson(route('contacts.index'))
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [['id', 'name', 'phone', 'email', 'cep']]
            ]);
    });

    it('can show a contact', function () {
        $contact = Contact::factory()->create([
            'name'   => 'Állison Luis',
            'phone'  => '21997651914',
            'number' => '43',
            'email'  => 'gierdiaz@hotmail.com',
            'cep'    => '23017-130',
        ]);

        getJson(route('contacts.show', $contact->id))
            ->assertStatus(200)
            ->assertJsonFragment([
                'id'     => $contact->id,
                'name'   => $contact->name,
                'number' => $contact->number,
                'phone'  => $contact->phone,
                'email'  => $contact->email,
                'cep'    => $contact->cep,
            ]);

    });

    it('can search contacts by cep', function () {
        Contact::factory()->create([
            'name'   => 'Állison Luis',
            'cep'    => '23017-130',
            'number' => '43',
            'email'  => 'gierdiaz@hotmail.com',
            'phone'  => '21997651914',
        ]);

        getJson(route('contacts.search', ['cep' => '23017-130']))
            ->assertStatus(200)
            ->assertJsonFragment([
                'name' => 'Állison Luis',
                'cep' => '23017-130'
            ]);
    });

    it('can store a contact', function () {
        $data = [
            'name'   => 'Állison Luis',
            'phone'  => '21997651914',
            'number' => '43',
            'email'  => 'gierdiaz@hotmail.com',
            'cep'    => '23017-130',
        ];

        $mock = Mockery::mock(ViaCepIntegration::class);
        $mock->shouldReceive('getAddressByCep')->andReturn((object) [
            'logradouro' => 'Rua Olinto da Gama Botelho',
            'bairro'     => 'Bairro RJ',
            'localidade' => 'Rio de Janeiro',
            'uf'         => 'RJ',
            'ibge'       => '3304557',
            'gia'        => '456',
            'ddd'        => '21',
            'siafi'      => '6001',
        ]);

        app()->instance(ViaCepIntegration::class, $mock);

        postJson(route('contacts.store'), $data)
            ->assertStatus(201)
            ->assertJson(['message' => 'Contato registrado com sucesso.']);

        $this->assertDatabaseHas('contacts', $data);
    });

    it('can update a contact', function () {
        $contact = Contact::factory()->create();

        $data = [
            'name'   => 'Pâmela Barbosa',
            'phone'  => '21992912611',
            'number' => '10',
            'email'  => 'schzimmyd@gmail.com',
            'cep'    => '21220-380',
        ];

        putJson(route('contacts.update', $contact->id), $data)
            ->assertStatus(200)
            ->assertJson(['message' => 'Contato atualizado com sucesso.']);

        $this->assertDatabaseHas('contacts', array_merge(['id' => $contact->id], $data));
    });

    it('can delete a contact', function () {
        $contact = Contact::factory()->create();

        deleteJson(route('contacts.destroy', $contact->id))
            ->assertStatus(200)
            ->assertJson(['message' => 'Contato removido com sucesso.']);

        $this->assertSoftDeleted('contacts', ['id' => $contact->id]);
    });
});
