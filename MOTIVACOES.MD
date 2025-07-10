# Motivações e Escolhas Técnicas

## 1. Contexto e Motivações
- O enunciado original do teste fala em usar uma API REST “pura”.  
- No ecossistema Drupal moderno, o módulo **JSON:API** já é a implementação de facto de uma API RESTful, providenciando:  
  - Endpoints padronizados sem escrever controllers.  
  - Conformidade com o padrão JSON:API (v1.1).  
  - Controle de acesso via permissões de entidade.  
  - Versionamento e extensibilidade built-in.  
- **Motivação principal**: aproveitar toda a infraestrutura pronta do Drupal e usar tecnologias atuais, evitando reinventar o “REST” manualmente.

## 2. Escolhas Técnicas
1. **Entidades Customizadas**  
   - As entidades `PollQuestion` e `PollVote` foram definidas como ContentEntityType customizados, distintos dos nodes padrão do Drupal, permitindo um modelo de dados mais enxuto e focado neste domínio.  
2. **Campo Composto de Opções**  
   - Inicialmente considerei criar cada opção como entidade separada, mas optei por um **FieldType** customizado (`poll_option_item`) que agrupa título, descrição e imagem.  
   - Vantagens: mantém tudo dentro da própria `PollQuestion`, simplifica gravação e serialização.  
3. **JSON:API em vez de REST “manual”**  
   - Evita criar controllers, rotas e serializers para cada operação.  
   - Ganha endpoints de listagem, criação, atualização e deleção prontinhos.  
   - Facilita consumo por front-ends externos (decoupled).  
4. **Contadores e Percentuais Persistidos**  
   - Em vez de calcular contagens e percentuais a cada requisição, mantemos três campos em `PollQuestion`:  
     - `total_votes` (inteiro)  
     - `option_counts` (JSON map de UUID→contagem)  
     - `option_percentages` (JSON map de UUID→percentual)  
   - Hooks em `poll_vote_insert` e `poll_vote_delete` atualizam esses campos no momento do insert/delete.  
   - Isso garante que o JSON:API retorne as estatísticas prontas, sem cálculo extra no cliente.  
5. **Permissões de Acesso por Entidade**
   - As permissões foram definidas diretamente nas entidades por meio de arquivos YAML e de um AccessControlHandler customizado.
   - Para facilitar o teste de API, concedi permissão de criação de votos ao usuário anônimo, eliminando a necessidade de fluxos de autenticação complexos.
6. **Lista Canônica de Poll Questions**
   - A listagem canônica de perguntas (`/admin/content/poll_question`) é gerada automaticamente pelo Drupal a partir de um ListBuilder personalizado.
   - A classe `PollQuestionListBuilder` estende `EntityListBuilder`, usando `buildHeader()` para colunas e `buildRow()` para preencher título, total de votos, barras de progresso e links de operação.
   - Essa abordagem declarativa garante consistência com outras listagens do Drupal e facilita futuras customizações.
7. **Remoção em Cascata de Votos**
   - Ao excluir uma `PollQuestion`, um hook de remoção (`hook_entity_delete`) apaga automaticamente todos os `poll_vote` associados.
   - Isso assegura integridade referencial e evita registros órfãos no banco de dados.

## 3. Inconsistências na Documentação Técnica do Teste
Embora o documento apresente o escopo geral do sistema de votação, alguns requisitos não especificam claramente o contexto de aplicação — se referem à interface administrativa, à visualização de conteúdo pelos usuários finais ou aos consumidores de API. Do mesmo modo, as orientações sobre configurações de funcionamento, permissões e operações externas ficam em termos amplos, exigindo suposições que podem levar a diferentes interpretações. Para garantir um comportamento previsível e alinhado às boas práticas do Drupal, optei por decisões arquiteturais e tecnológicas que preenchem essas lacunas de forma estruturada e padronizada.

## 4. Conclusão
Este projeto aproveita ao máximo as features **built-in** do Drupal e do JSON:API, resultando em:
- Menor quantidade de código customizado.  
- Endpoints imediatamente disponíveis.
- Facilidade de expansão futura (por exemplo, adicionar filtros, bulk operations ou views).  
- Cliente externo recebe, em uma única chamada, tanto as perguntas quanto as estatísticas (total e percentuais) já calculadas.  