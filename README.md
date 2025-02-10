# Inorganica
Projeto para melhoria de comportamento humano em reciclagem baseado em recompensa.
![Logo](https://github.com/MauroNadalin/Inorganica/blob/main/img/tampinhalogo.png)

# Reciclagem Inorgânica

Aqui vai uma breve descrição do seu projeto.

## Índice

- [Visão Geral](#visao-geral)
- [Arquivos do Projeto](#arquivos-do-projeto)
  - [arquivo1.ext](#arquivo1ext)
  - [arquivo2.ext](#arquivo2ext)
  - [arquivo3.ext](#arquivo3ext)

## Visão Geral

Este é o repositório do meu projeto. Aqui você encontra uma explicação geral sobre o que o projeto faz e qual a sua finalidade.
[Abrir Manual em PDF](https://docs.google.com/viewer?url=https://github.com/MauroNadalin/Inorganica/raw/main/manual.pdf)

## Arquivos do Projeto

Abaixo, estão os arquivos presentes no repositório e suas respectivas funções.

### `abre_ou_redireciona.php`

Utilizado para controle de acesso da página de cadastro de usuários. Uma vez que para se cadastrar como novo usuário obviamente este novo usuário ainda não tem login, então ele precisa ter acesso a essa página, mas passando primeiro pela pagina inicial, onde existe o redirecionamento via menu.

### `atebreve.php`

Este arquivo é usado para controlar e garantir que os dados de sessão sejam apagados quando o usuário fizer logout.

### `bgpagina.jpg`

Usado para imagem de fundo das telas.

### `cadusu.php`

Arquivo manipula conexão com banco de dados, apresenta formulario para preencher dados e faze consulta "INSERT" via método POST para 
salvar os dados cadastrais de novo usuário.

### `coletacoletor.php`

Arquivo manipula conexão com banco de dados, apresenta formulario e lista de coletas pendentes, disponíveis, e reservadas, para que possam ser tratadas pelos coletores. Esta página é mostrada apenas para usuários com perfil "coletor", e também contém botões de seleção e ação para tratar as coletas, e alterar valores do banco de dados como datas, status e nome do coletor.

### `coletaprov.php`

Manipula conexão com banco de dados e apreenta formulario de coletas em tratamento envolvidas com o provedor logado. É mostrado apenas para usuários com perfil "provedor". É onde o provedor faz o cadastramento de uma coleta, para iniciar o processo de tratativa.

### `conexao.php - config.php`

Define as propriedades de conexão com o banco de dados, e as variáveis de objetos PDO para execução de consultas utilzando 'statments', para evitar ataques a banco de dados atacados por "SQL Injection"

### `exportar.php`

Gera um arquivo tipo "txt" e disponibiliza para download. A função é exportar as informações do usuário logado para que possa ser tratada de forma subjetiva, de acordo com a necessidade de cada usuário, utilizando importação de dados via excel, google sheets, etc...

### `favicon.ico`

Arquivo que é buscado pelo browser, por padrão, para ser mostrado na aba da janela do navegador. Neste caso colocamos a figura de nosso mascote e renomeamos para favicon.ico, assim ele aparece na aba dos navegadores.

### `login.php - logout.php`

O arquivo login.php é usado para validar o login e senha via consulta ao banco de dados, comparando login e senha digitados no formulario com aquele presente no registro do usuário. Uma vez validada, são definidas as variáveis de sessão, utilizadas para restringir, permitir ou redirecionar o fluxo de apresentações de telas. O arquivo de logout serve para limpar as variáveis de sessão e garantir a parada de qualquer outro processamento (comando exit()),e redireciona para a página "atebreve";

### `processar_nota.php`

Faz o tratamento da nota atribuída ao provedor, pelo coletor, no momento da ação de 'coleta', quando o status passa para coletado. A nota deve ser somada a nota ja existente no campo "pontos" da tabela "users"

### `relatorios.php - rel.php`

São os arquivos utilizados para gerar as consultas e apresentar os resultados na tela, ou fornecer também a função de exportação atraves de um botão de ação. Os relatórios são apresentados na tela em forma de tabela, e possuem filtros de status, data inicio e fim, nome, etc..

### `arquivos .css`

os arquivos .css são os arquivos de estilos utilizados para a definição de formatos, cores, tamanhos e formas, a fim de proporcionar uma estética atraente e original para as páginas. Também nas folhas de estilo definimos containers do tipo "flex" que permitem melhor apresentação tanto para PC, como para notebooks, tablets e aparelhos móveis.

### `Pasta global`

Na pasta global estão colocados arquivos de configuração do servidor web, e do sql, para consultar quando migrar de um servidor de testes para o servidor de produção. O arquivo 50-server.conf definimos a propriedade bind-adress para 0.0.0.0 para permitir conexão remota(para acessar via app) e o arquivo apache2 contém as configurações do servidor web para este projeto específico. O arquivo confs.notes é onde anotamos as informações acerca desses dois arquivos.
## Como Executar o Projeto

O projeto exige a instalação e configuração de vários itens, os quais vamos descrever resumidamente nesta seção, partindo um servidor com sistema operacional Linux Debian12 operacional.
Basicamente, os tipos de serviços que necessitamos são: Servidor WEB, PHP, Servidor de Banco de Dados(Mysql ou MariaDB). 

Para disponibilizar estes serviços, seguimos os seguintes procedimentos:

### Passo 1: Atualizar o Sistema

sudo apt update
sudo apt upgrade -y


### Passo 2: Instalar Apache2
Instale o servidor web Apache2:

sudo apt install apache2 -y

Habilite os módulos necessários:

sudo a2enmod rewrite
sudo a2enmod headers

Reinicie o Apache para aplicar as mudanças:

sudo systemctl restart apache2


### Passo 3: Instalar PHP
Instale o PHP e os módulos necessários:

sudo apt install php libapache2-mod-php php-mysql php-cli php-pear php-gmp php-gd php-bcmath php-mbstring php-curl php-xml php-zip -y

Reinicie o Apache novamente:

sudo systemctl restart apache2

### Passo 4: Instalar MariaDB

Instale o servidor e cliente MariaDB:

sudo apt install mariadb-server mariadb-client -y

Proteja a instalação do MariaDB:

sudo mysql_secure_installation

Siga as instruções para definir a senha root e configurar a segurança.

### Passo 5: Testar a Instalação
Crie um arquivo PHP de teste:

sudo nano /var/www/html/info.php

Adicione o seguinte conteúdo:

<?php
phpinfo();
?>

Acesse `http://seu_ip_servidor/info.php` no navegador para verificar se o PHP está funcionando corretamente.

### Passo 6: Configurar o MariaDB
Acesse o MariaDB:

sudo mysql -u root -p

Crie um banco de dados e um usuário:
sql
CREATE DATABASE meu_banco;
CREATE USER 'meu_usuario'@'localhost' IDENTIFIED BY 'minha_senha';
GRANT ALL PRIVILEGES ON meu_banco.* TO 'meu_usuario'@'localhost';
FLUSH PRIVILEGES;
EXIT;

### Passo 7: Instalar phpMyAdmin (Opcional)
Instale o phpMyAdmin para gerenciar o MariaDB via interface web:

sudo apt install phpmyadmin -y

Selecione Apache2 durante a instalação e configure o phpMyAdmin conforme necessário.

### Passo 8: Reiniciar Todos os Serviços
Reinicie o Apache e o MariaDB para garantir que todas as mudanças sejam aplicadas:

sudo systemctl restart apache2
sudo systemctl restart mariadb

Com essas ações criamos o ambiente para desenvolver os códigos a fim de atender as necessidades de fluxo e processamento necessárias ao projeto.

## Contribuições

Para contribuir com o projeto, estamos planejando a criação de caixas para acondicionamento de materiais recicláveis inogânicos, com travas eletromecânicas que irão abrir via bluetooth a partir dos aparelhos celulares dos coletores e provedores. Isto serviria para proteger o trabalho de ambos, evitando o acesso a pessoas não autorizadas, e formando uma rede de ajuda mútua. Informações ou sugestões podem enviar email para: mnadalin@alunos.utfpr.edu.br
![Logo2](https://github.com/MauroNadalin/Inorganica/blob/main/tampinha2.png)
### OBRIGADO!!

