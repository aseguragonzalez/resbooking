<?php




/**
 * Implementación de la interfaz DAO basado en la clase StmtClient
 * para el acceso a la base de datos
 *
 * @author alfonso
 */
class StmtBaseDAO implements \IDataAccessObject{

    /**
     * Referencia al gestor de trazas
     * @var \ILogManager Referencia al gestor de trazas
     */
    private $_log = null;

    /**
     * Atributo para generar trazas por cada consulta ejecutada
     * @var boolean Indica si está en modo debug
     */
    private $_isDebug = false;

    /**
     * Referencia al cliente Stmt para el acceso a base de datos
     * @var \SmtpClient Referencia al cliente Stmt mysqli
     */
    protected $StmtClient = null;

    /**
     * Referencia al cliente validador de entidades
     * @var IValidatorClient Referencia al cliente de validación
     */
    protected $ValidatorClient = null;

    /**
     * Establece si el modo de trabajo es en depuración
     * (Generando trazas de consultas)
     */
    private function SetDebug(){
        // Evaluar si está definido el modo depuración
        $this->_isDebug = (DEBUG == 1);
    }

    /**
     * Configuración de las dependencias
     */
    private function SetReferences(){
        // Obtener referencia al inyector de dependencias
        $injector = Injector::GetInstance();
        // Obtener referencia al gestor de trazas
        $this->_log = $injector->Resolve( "ILogManager" );
    }

    /**
     * Generación de trazas de las consultas a ejecutar
     * @var string $method Método que ejecuta la consulta
     * @var string $entity Nombre de la entidad relacionada
     * @var string $query Cunslta sql a ejecutar
     */
    protected function LogQuery( $method = "", $entity = "" ,$query = "" ){
        // Comprobación del modo de trabajo
        if(!$this->_isDebug) {
            return;
        }
        // Comprobación de la referencia al gestor de trazas
        if($this->_log == null) {
            return;
        }
        // Creación del mensaje
        $message = $method . " - ".$entity." - ".$query;
        // Generar traza en modo info
        $this->_log->LogInfo( $message );
    }

    /**
     * Constructor por defecto de la clase
     */
    public function __construct(){
        // Establecer el modo de trabajo
        $this->SetDebug();
        // Establecer las referencias de los atributos
        $this->SetReferences();
    }

    /**
     * Permite configurar los parámetros de la conexión al
     * sistema de persistencia.
     * @var array $connection Datos de la conexión
     */
    public function Configure($connection = null){
        // Validación del parámetro
        if ($connection == null){
            return;
        }
        // Obtener Instancia del dao
        $this->StmtClient =
                StmtClient::GetInstance(null, $connection["filename"]);
        // Obtener referencia al validador de entidades
        $this->ValidatorClient =
                ValidatorClient::GetInstance($connection["filename"]);
    }

    /**
     * Persiste la entidad en el sistema y la retorna actualizada
     * @var object $entity Referencia a la entidad
     */
    public function Create($entity){
        // Obtener el nombre de la entidad
        $entityName = get_class($entity);
        // Obtener la consulta
        $select = $this->StmtClient->GetCreateQuery($entityName);
        // Generar la traza de la consulta
        $this->LogQuery( "Create", $entityName, $select);
        // Configurar los parámetros
        $this->StmtClient->SetParameters($entity);
        // Abrir conexión
        $this->StmtClient->Open();
        // Ejecutar la consulta
        $id = $this->StmtClient->Execute($select);
        // Cerrar conexión
        $this->StmtClient->Close();
        // Retornar Id generado
        return $id;
    }

    /**
     * Obtiene una entidad filtrada por su identidad utilizando
     * el nombre del tipo de entidad
     * @var object $identity Identidad de la entidad
     * @var string $entityName Nombre de la entidad
     */
    public function Read($identity, $entityName){
        // Obtener la consulta
        $select = $this->StmtClient->GetReadQuery( $entityName );
        // Generar la traza de la consulta
        $this->LogQuery( "Read", $entityName, $select);
        // Obtener instancia de la entidad
        $entity = $this->StmtClient->GetEntity( $entityName, $identity );
        // Configurar los parámetros
        $this->StmtClient->SetParameters( $entity );
        // Abrir conexión
        $this->StmtClient->Open();
        // Ejecutar la consulta
        $result =	$this->StmtClient->ExecuteQuery( $select, $entityName );
        // Cerrar conexión
        $this->StmtClient->Close();
        // Retornar la referencia (si existe)
        return (count($result) >0)? $result[0] : null;
    }

    /**
     * Actualiza la información de la entidad en el sistema de persistencia.
     * @var object $entity Referencia a la entidad
     */
    public function Update($entity){
        // Obtener el nombre de la [entidad | clase]
        $entityName = get_class($entity);
        // Obtener la consulta
        $select = $this->StmtClient->GetUpdateQuery($entityName);
        // Generar la traza de la consulta
        $this->LogQuery( "Update", $entityName, $select);
        // Configurar los parámetros
        $this->StmtClient->SetParameters($entity);
        // Abrir conexión
        $this->StmtClient->Open();
        // Ejecutar la consulta
        $data = $this->StmtClient->Execute($select);
        // Cerrar conexión
        $this->StmtClient->Close();
        // Retornar el resultado de la consulta
        return $data;
    }

    /**
     * Elimina la entidad utilizando su identidad y el nombre del
     * tipo de entidad
     * @var object $identity Identidad de la entidad
     * @var string $entityName Nombre de la entidad
     */
    public function Delete($identity, $entityName){
        // Obtener la consulta
        $select = $this->StmtClient->GetDeleteQuery($entityName);
        // Generar la traza de la consulta
        $this->LogQuery( "Delete", $entityName, $select);
        // Obtener instancia de la entidad
        $entity = $this->StmtClient->GetEntity( $entityName, $identity );
        // Configurar los parámetros
        $this->StmtClient->SetParameters($entity);
        // Abrir conexión
        $this->StmtClient->Open();
        // Ejecutar la consulta
        $data = $this->StmtClient->Execute($select);
        // Cerrar conexión
        $this->StmtClient->Close();
        // Retornar el resultado de la operación
        return $data;
    }

    /**
     * Obtiene el conjunto de entidades existentes del tipo especificado
     * @var string $entityName Nombre de la entidad
     */
    public function Get($entityName){
        // Obtener la consulta
        $select = $this->StmtClient->GetListQuery($entityName);
        // Generar la traza de la consulta
        $this->LogQuery( "Get", $entityName, $select);
        // Abrir conexión
        $this->StmtClient->Open();
        // Ejecutar la consulta
        $result = $this->StmtClient->ExecuteQuery($select, $entityName);
        // Cerrar conexión
        $this->StmtClient->Close();
        // Retornar el resultado
        return $result;
    }

    /**
     * Obtiene el conjunto de entidades del tipo especificado mediante
     * el filtro especificado. El filtro debe ser un array:
     * array( "Campo1" => valor1, "Campo2" => valor2... )
     * @var string $entityName Nombre de la entidad
     * @var array $filter Filtro de búsqueda
     */
    public function GetByFilter($entityName, $filter){
        // Obtener la consulta
        $select = $this->StmtClient->GetFilterQuery($entityName, $filter);
        // Generar traza de la consulta
        $this->LogQuery( "GetByFilter", $entityName, $select);
        // Instanciar entidad
        $entity = $this->StmtClient->SetEntity( $entityName, $filter);
        // Setear los parámetros
        $this->StmtClient->SetParameters($entity);
        // Abrir conexión
        $this->StmtClient->Open();

        // Ejecutar la consulta
        $result = $this->StmtClient->ExecuteQuery($select, $entityName);
        // Cerrar la conexión
        $this->StmtClient->Close();
        // Retornar el resultado
        return $result;
    }

    /**
     * Ejecuta la consulta pasada como parámetro
     * @var string $query Consulta sql a ejecutar
     */
    public function ExeQuery($query){
        // Abrir conexión
        $this->StmtClient->Open();
        // Ejecutar la consulta
        $this->StmtClient->ExecuteQuery($query);
        // Cerrar conexión
        $this->StmtClient->Close();
    }

    /**
     * Valida el contenido de una entidad
     * @var object $entity Referencia a la entidad a validar
     */
    public function IsValid($entity){
        // Comprobar la referencia
        if( $this->ValidatorClient == null) {
            return array();
        }
        // validación de la entidad
        return $this->ValidatorClient->Validate($entity);
    }
}
