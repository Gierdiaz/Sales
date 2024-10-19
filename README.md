# Agenda de Contatos

Esta é uma aplicação web desenvolvida em PHP utilizando o framework Laravel para gerenciar contatos. A aplicação permite a adição, busca, atualização e remoção de contatos, além de integrar com a API ViaCEP para validar endereços.

## Requisitos

Antes de começar, verifique se você possui as seguintes ferramentas instaladas em sua máquina:

- [Docker](https://www.docker.com/get-started)
- [Docker Compose](https://docs.docker.com/compose/install/)

## Configuração do Ambiente

### 1. Clone o Repositório

git clone https://github.com/Gierdiaz/Laravel.git

###  2. Crie um arquivo .env
Copie o arquivo .env.example e renomeie-o para .env. Configure as variáveis de ambiente conforme necessário.

```bash
cp .env.example .env
```
Configure as seguintes variáveis de ambiente para o banco de dados MySQL:
```plaintext
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=application
DB_USERNAME=root
DB_PASSWORD=root
```


###  3. Inicie os Containers Docker
Execute o seguinte comando para iniciar os containers Docker:
```bash
docker-compose up -d
```
###  4. Instale as Dependências do Composer
Para instalar as dependências do Composer, execute:

```bash
docker exec app composer install
```
###  5. Execute as Migrações do Banco de Dados
Para criar as tabelas no banco de dados, execute:
```bash
docker exec app php artisan migrate:fresh --seed
```

# Acessar o MySQL no container
Depois que o container do MySQL estiver em execução, você pode acessá-lo diretamente usando o seguinte comando:
```bash
docker exec -it mysql mysql -u root -p
```
Ao rodar esse comando, você será solicitado a inserir a senha de root que definiu no docker-compose.yml (root neste caso).

Comandos para gerenciar o banco de dados
Após acessar o MySQL no container, você pode executar os seguintes comandos:

###  Listar bancos de dados:
```sql
SHOW DATABASES;
```
###  Criar um banco de dados:
```sql
CREATE DATABASE nome_do_banco;
```

### Excluir um banco de dados:
```sql
DROP DATABASE nome_do_banco;
```

### Conectar a um banco de dados específico:
```sql
USE nome_do_banco;
```

### Visualizar tabelas em um banco de dados:
```sql
SHOW TABLES;
```

# Comandos de Teste
Executando os Comandos do Projeto
Para executar os testes e ferramentas de análise de código, utilize os seguintes comandos:

### Para executar os testes com Pest:

```bash
docker exec app ./vendor/bin/pest
```
### Para executar o PHP Code Sniffer (Pint):

```bash
docker exec app ./vendor/bin/pint
```
### Para analisar o código com PHPStan:

```bash
docker exec app ./vendor/bin/phpstan analyse --memory-limit=2G
```
# Endpoints da API

## Autenticação

### Registrar Usuário

- **Método:** POST  
- **Endpoint:** `/api/auth/register`  
- **Corpo da Requisição:**
```json
    {
      "name": "Nome do Usuário",
      "email": "email@example.com",
      "password": "senha123",
      "password_confirmation": "senha123"
    } 
```
- **Resposta:**
```json
    {
      "message": "User registered successfully"
    }
```
### Login

- **Método:** POST  
- **Endpoint:** `/api/auth/login`  
- **Corpo da Requisição:**
```json
    {
      "email": "email@example.com",
      "password": "senha123"
    }
```

- **Resposta:**
```json
{
  "access_token": "TOKEN_DE_AUTENTICAÇÃO",
  "token_type": "Bearer"
}
```
### Logout

- **Método:** POST  
- **Endpoint:** `/api/auth/logout`  
- **Autenticação:** Bearer Token  
- **Resposta:**
```json
  {
    "message": "Logged out successfully"
  }
```
## Contatos

### Listar Contatos

- **Método:** GET  
- **Endpoint:** `/api/v1/contacts`  
- **Resposta:**
```json
    "data": [
      {
        "id": "550e8400-e29b-41d4-a716-446655440000" ,
        "name": "Nome do Contato",
        "phone": "123456789",
        "email": "contato@example.com",
        "number": "10",
        "cep": "01001-000",
        "address": "Praça da Sé, Sé, São Paulo - SP",
        "links": {
          "show": "/api/contacts/550e8400-e29b-41d4-a716-446655440000"
        }
      }
    ]
```

### Mostra Contatos

- **Método:** GET  
- **Endpoint:** `/api/v1/contacts`  
- **Resposta:**
```json
    "data": {
        "id": "550e8400-e29b-41d4-a716-446655440000",
        "name": "Nome do Contato",
        "phone": "123456789",
        "email": "contato@exemplo.com",
        "number": "10",
        "cep": "01001-000",
        "address": "Endereço, Bairro, Cidade - UF",
        "links": {
            "index": "/api/contacts"
        }
    }
```
### Buscar Contato

- **Método:** GET  
- **Endpoint:** `/api/v1/contacts/search`  
- **Parâmetros:** `name`, `email`, `cep`, `number`  
- **Resposta:**
```json
    "data": [
      {
        "id": "550e8400-e29b-41d4-a716-446655440000",
        "name": "Nome do Contato",
        "phone": "123456789",
        "email": "contato@example.com",
        "number": "10",
        "cep": "01001-000",
        "address": "Praça da Sé, Sé, São Paulo - SP"
      }
    ]
```

### Adicionar Contato

- **Método:** POST  
- **Endpoint:** `/api/v1/contacts`  
- **Corpo da Requisição:**
```json
  {
    "name": "Nome do Contato",
    "phone": "123456789",
    "email": "contato@example.com",
    "number": "10",
    "cep": "01001-000"
  }
```

- **Resposta:**
```json
    "data": {
        "id": "550e8400-e29b-41d4-a716-446655440000",
        "name": "Nome do Contato",
        "phone": "123456789",
        "email": "contato@exemplo.com",
        "number": "10",
        "cep": "01001-000",
        "address": "Endereço, Bairro, Cidade - UF"
    }  
```

### Atualizar Contato

- **Método:** PUT  
- **Endpoint:** `/api/v1/contacts/{id}`  
- **Corpo da Requisição:**
```json
  {
    "name": "Nome Atualizado",
    "phone": "987654321",
    "email": "contato_atualizado@example.com",
    "number": "20",
    "cep": "02002-000"
  }
```
- **Resposta:**
```json
    {
      "message": "Contato atualizado com sucesso."
    }
```
### Excluir Contato

- **Método:** DELETE  
- **Endpoint:** `/api/v1/contacts/{id}`  
- **Resposta:**
```json
  {
    "message": "Contato excluído com sucesso."
  }
```

