<?php

namespace Tests\Feature;

use App\Integrations\ViaCepIntegration;
use App\Models\{Contact, User};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use function Pest\Laravel\{deleteJson, getJson, postJson, putJson};

uses(RefreshDatabase::class);

describe('Managing Contact Records', function () {
    beforeEach(function () {
        DB::table('users')->insert([
            'name' => 'Gierdiaz',
            'email' => 'gierdiaz@hotmail.com',
            'password' => bcrypt('password'),
        ]);

        $this->user = User::where('email', 'gierdiaz@hotmail.com')->first();
        Sanctum::actingAs($this->user);
    });

    it('can list contacts', function () {
        DB::table('contacts')->insert([
            ['name' => 'Állison Luis', 'phone' => '2197651914', 'email' => 'gierdiaz@hotmail.com', 'cep' => '23017-130'],
        ]);

        getJson(route('contacts.index'))
            ->assertStatus(200)
            ->assertJsonStructure(['data' => [['id', 'name', 'phone', 'email', 'cep']]]);
    });

    it('can show a contact', function () {
        DB::table('contacts')->insert([
            'id'     => Str::uuid(),
            'name' => 'Állison Luis',
            'phone' => '2197651914',
            'email' => 'gierdiaz@hotmail.com',
            'cep' => '23017-130',
        ]);

        $contact = Contact::where('email', 'gierdiaz@hotmail.com')->first();

        getJson(route('contacts.show', $contact->id))
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id'    => $contact->id,
                    'name'  => $contact->name,
                    'email' => $contact->email,
                    'cep'   => $contact->cep,
                    'links' => ['index' => route('contacts.index')],
                ],
            ]);
    });

    it('can search contacts by cep', function () {
        DB::table('contacts')->insert([
            'id'     => Str::uuid(),
            'name'   => 'Állison Luis',
            'cep'    => '23017-130',
            'email'  => 'gierdiaz@hotmail.com',
            'phone'  => '2197651914',
            'number' => '43',
        ]);

        getJson(route('contacts.search', ['cep' => '23017-130']))
            ->assertStatus(200)
            ->assertJsonFragment(['name' => 'Állison Luis', 'cep' => '23017-130']);

        getJson(route('contacts.search', ['cep' => '99999-999']))
            ->assertStatus(404)
            ->assertJson(['message' => 'Nenhum contato encontrado para o termo fornecido.']);
    });

    it('can store a contact', function () {
        $data = [
            'id'     => Str::uuid(),
            'name'   => 'Állison Luis',
            'phone'  => '2197651914',
            'email'  => 'gierdiaz@hotmail.com',
            'number' => '43',
            'cep'    => '23017-130',
        ];

        $addresses = [
            '23017-130' => [
                'logradouro' => 'Rua Exemplo RJ',
                'bairro'     => 'Bairro RJ',
                'localidade' => 'Rio de Janeiro',
                'uf'         => 'RJ',
                'ibge'       => '3304557',
                'gia'        => '1004',
                'ddd'        => '21',
                'siafi'      => '7107',
            ],
        ];

        $mock = \Mockery::mock(ViaCepIntegration::class);
        $mock->shouldReceive('getAddressByCep')
            ->with($data['cep'])
            ->andReturn($addresses[$data['cep']]);

        app()->instance(ViaCepIntegration::class, $mock);

        postJson(route('contacts.store'), $data)
            ->assertStatus(201)
            ->assertJson(['message' => 'Contato criado com sucesso.']);

        $this->assertDatabaseHas('contacts', array_merge($data, $addresses[$data['cep']]));
    });

    it('can update a contact', function () {
        DB::table('contacts')->insert([
            'id'     => Str::uuid(),
            'name'   => 'Old Name',
            'phone'  => '111111111',
            'email'  => 'old@example.com',
            'number' => '10',
            'cep'    => '00000-000',
        ]);

        $contact = Contact::where('email', 'old@example.com')->first();

        $data = [
            'name'   => 'Jane Doe',
            'phone'  => '987654321',
            'email'  => 'jane@example.com',
            'number' => '20',
            'cep'    => '02002-000',
        ];

        $mock = \Mockery::mock(ViaCepIntegration::class);
        $mock->shouldReceive('getAddressByCep')
            ->with(\Mockery::on(fn ($cep) => $cep === $data['cep']))
            ->andReturn([
                'logradouro' => 'Rua da Luz',
                'bairro'     => 'Luz',
                'localidade' => 'São Paulo',
                'uf'         => 'SP',
                'cep'        => $data['cep'],
                'ibge'       => '3550308',
                'gia'        => '1004',
                'ddd'        => '11',
                'siafi'      => '7107',
            ]);

        app()->instance(ViaCepIntegration::class, $mock);

        putJson(route('contacts.update', $contact->id), $data)
            ->assertStatus(200)
            ->assertJson(['message' => 'Contato atualizado com sucesso.']);

        $this->assertDatabaseHas('contacts', [
            'id'     => $contact->id,
            'name'   => $data['name'],
            'phone'  => $data['phone'],
            'email'  => $data['email'],
            'number' => $data['number'],
            'cep'    => $data['cep'],
        ]);
    });

    it('can delete a contact', function () {
        DB::table('contacts')->insert([
            'id'     => Str::uuid(),
            'name'   => 'Állison Luis',
            'phone'  => '2197651914',
            'email'  => 'gierdiz@hotmail.com',
            'cep'    => '23017-130',
        ]);

        $contact = Contact::where('email', 'gierdiz@hotmail.com')->first();

        deleteJson(route('contacts.destroy', $contact->id))
            ->assertStatus(200)
            ->assertJson(['message' => 'Contato removido com sucesso.']);

        $this->assertSoftDeleted('contacts', ['id' => $contact->id]);
    });
});
