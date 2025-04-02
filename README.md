# Agenda de Contatos

Esta é uma aplicação web desenvolvida em PHP utilizando o framework Laravel para gerenciar contatos. A aplicação permite a adição, busca, atualização e remoção de contatos, além de integrar com a API ViaCEP para validar endereços.

## Princípios de Design Utilizados

- **SOLID**: A aplicação segue os princípios SOLID para garantir que o código seja escalável, manutenível e de fácil compreensão.
- **Clean Architecture**: A arquitetura da aplicação é baseada no conceito de Clean Architecture, separando as preocupações e facilitando testes e manutenção.
- **Repository Pattern**: Utiliza-se o padrão Repository para abstrair a lógica de acesso a dados, permitindo uma maior flexibilidade e testabilidade.
- **Data Transfer Object (DTO)**: Os objetos de transferência de dados (DTO) são utilizados para encapsular os dados que serão transferidos entre as camadas da aplicação.
- **Integração com API**: A aplicação integra-se com a API Via CEP para validar e obter informações de endereço a partir do CEP fornecido pelo usuário.
- **API RESTful com Maturidade de Richardson e HATEOAS**: Os endpoints da API são projetados seguindo os princípios RESTful e incorporam a maturidade de Richardson para oferecer diferentes níveis de abstração. Além disso, implementamos HATEOAS para fornecer links que permitem a navegação entre recursos relacionados, melhorando a interatividade e a descoberta da API.

## Requisitos

Antes de começar, verifique se você possui as seguintes ferramentas instaladas em sua máquina:

- [Docker](https://www.docker.com/get-started)
- [Docker Compose](https://docs.docker.com/compose/install/)

## Configuração do Ambiente

### 1. Clone o Repositório
```bash
git clone https://github.com/Gierdiaz/Agenda.git
```

### 2. Navegue até o Diretório do Projeto
```bash
cd Agenda
```

###  3. Crie um arquivo .env
Copie o arquivo .env.example e renomeie-o para .env. Configure as variáveis de ambiente conforme necessário.

- **Linux/macOS**:
```bash
cp .env.example .env
```

- **Windows (CMD)**:
```bash
copy .env.example .env
```

Exemplo para configurar as seguintes variáveis de ambiente para o banco de dados MySQL:
```plaintext
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=application
DB_USERNAME=root
DB_PASSWORD=root
```

###  4. Construir e Iniciar os Contêineres
Para construir a imagem Docker e iniciar todos os serviços, execute:
```bash
docker-compose up --build -d
```

###  5. Instale as Dependências do Composer
Para instalar as dependências do Composer, execute:
```bash
docker exec app composer install
```

###  6. Gerar uma Nova Chave de Criptografia
Use o Artisan para gerar uma nova chave de criptografia
```bash
docker-compose exec app php artisan key:generate
```

### 7. Acesse a Aplicação
Após a construção e inicialização dos contêineres, você poderá acessar a aplicação em:
```bash
http://localhost:8000
```

### 8.  Parar os Contêineres
Para parar os contêineres, você pode usar:
```bash
docker-compose down
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

###  9. Execute as Migrações do Banco de Dados
Para criar as tabelas no banco de dados, execute:
```bash
docker exec app php artisan migrate:fresh --seed
```

# Comandos de Teste
Executando os Comandos do Projeto
Para executar os testes e ferramentas de análise de código, utilize os seguintes comandos:

### Para executar os testes com Pest:
Executa os testes automatizados da aplicação usando o Pest
```bash
docker exec app ./vendor/bin/pest
```
### Para executar o PHP Code Sniffer (Pint):
Formata e verifica o código PHP da sua aplicação, garantindo que ele siga os padrões de codificação.
```bash
docker exec app ./vendor/bin/pint
```
### Para analisar o código com PHPStan:
Ferramenta de análise estática que ajuda a detectar erros no código antes mesmo da execução.
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
    "email": "usuario@example.com",
    "password": "SenhaSegura123!",
    "password_confirmation": "SenhaSegura123!"
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
  "email": "usuario@example.com",
  "password": "SenhaSegura123!"
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
            "id": "0195f7c5-92a4-701c-b342-5361b3e2af18",
            "name": "Állison Luis",
            "phone": "21997651914",
            "email": "gierdiaz@hotmail.com",
            "number": "43",
            "cep": "23017-130",
            "address": {
                "uf": "",
                "ddd": "21",
                "gia": "",
                "ibge": "3304557",
                "siafi": "6001",
                "bairro": "Campo Grande",
                "estado": "RJ",
                "regiao": "",
                "unidade": "",
                "localidade": "",
                "logradouro": "Rua Olinto da Gama Botelho",
                "complemento": ""
            },
            "links": {
                "show": "http://localhost:8000/api/v1/contacts/0195f7c5-92a4-701c-b342-5361b3e2af18"
            }
        },
]
```

### Mostra Contatos

- **Método:** GET  
- **Endpoint:** `/api/v1/contacts`  
- **Resposta:**
```json
{
    "data": {
        "id": "0195f7c5-92a4-701c-b342-5361b3e2af18",
        "name": "Állison Luis",
        "phone": "21997651914",
        "email": "gierdiaz@hotmail.com",
        "number": "43",
        "cep": "23017-130",
        "address": {
            "uf": "",
            "ddd": "21",
            "gia": "",
            "ibge": "3304557",
            "siafi": "6001",
            "bairro": "Campo Grande",
            "estado": "RJ",
            "regiao": "",
            "unidade": "",
            "localidade": "",
            "logradouro": "Rua Olinto da Gama Botelho",
            "complemento": ""
        },
        "links": {
            "index": "http://localhost:8000/api/v1/contacts"
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
        "cep": "22070-012",
        "address": "Praia de Copacabana, Rio de Janeiro - RJ"
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
{
    "message": "Contato registrado com sucesso.",
    "data": {
        "id": "0195f7c5-92a4-701c-b342-5361b3e2af18",
        "name": "Állison Luis",
        "phone": "21997651914",
        "email": "gierdiaz@hotmail.com",
        "number": "43",
        "cep": "23017-130",
        "address": {
            "logradouro": "Rua Olinto da Gama Botelho",
            "complemento": "",
            "unidade": "",
            "bairro": "Campo Grande",
            "localidade": "",
            "uf": "",
            "estado": "RJ",
            "regiao": "",
            "ibge": "3304557",
            "gia": "",
            "ddd": "21",
            "siafi": "6001"
        }
    }
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

## Executando os testes no Commit antes de fazer o push

Quando você faz um commit, o hook pre-commit executa automaticamente os testes e análises de código para ter certeza que todas as alterações irão subir sem nenhum erro. Veja um exemplo do que acontece:

![Resultado do Pre-Commit](image.png)
