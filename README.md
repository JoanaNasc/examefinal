# examefinal

Para a testar este projecto é necessario:
-> fazer chamadas guzzle e passar os parametros necessários a partir do JSON:
   	->login():
          - retorna a hash e o csrf_token precisos para passar aos metodos seguintes
          - Passar e-mail e password em json
	  ->register():
          - passar e-mail.
    ->alter():
          - passar e-mail, password enviada por e-mail e nova passaword
    ->getAllIndexes($hash):
          - passar hash.
    -> getIndexByName('AUS200',$hash):
          - passar o name (symbol) e $hash.
    ->getAllLogs($hash):
          - passar hash.
    ->getAllLogsCSV($hash):
          - passar hash.
    ->saveCSVMoreThan7k($hash)
           - passar hash.
    ->saveCSVLessThan7k($hash)
            - passar hash.
          
          
          
Quando se chama o login deve-se guardar o que retorna.
