<?php




/**
 * Clase de acceso a datos utilizando la clase de mysqli Stmt
 * ref : http://php.net/manual/es/class.mysqli-stmt.php
 *
 * @author alfonso
 */
class StmtClient {

    /**
     * Referencia a la instancia existente
     * @var \StmtClient Referencia al cliente StmtClient actual
     */
    private static $_singleton = null;

    /**
     * Nombre del fichero de configuración y mapeado de la bbdd
     * @var string $filename Nombre del fichero de configuración
     */
    private $_filename;

    /**
     * Array con los parámetros de conexión :
     * array ( "server" => "server", "user" => "user",
     * "password" => "password", "scheme" => "scheme");
     * @var array $_oConnData array de datos de conexión
     */
    private $_oConnData = null;

    /**
     * Nombre de la base de datos
     * @var string Nombre de la base de datos
     */
    private $_dbName;

    /**
     * Colección de tablas y vistas definidas
     * @var array $_dbObjects Array de objetos mapeados
     */
    private $_dbObjects = array();

    /**
     * Referencia a la instancia de conexión mysqli
     * @var object $_oConn Referencia al objeto de conexión
     */
    private $_oConn = null;

    /**
     * Cadena con los tipos de parámetros declarados
     * @var string Cadena de tipología de datos
     */
    private $_strTypes = "";

    /**
     * Array de parámetros a utilizar en la consulta
     * @var array $_parameters Array de parámetros a utilizar
     */
    private $_parameters = array();

    /**
     * Consulta SQL a ejecutar
     * @var string
     */
    private $_query = "";

    /**
     * Nombre del campo PK que se debe utilizar
     * @var string
     */
    private $_pkname = "";

    /**
     * Clausula WHERE de la consulta sql
     * @var string
     */
    private $_where = "";

    /**
     * Nombre de la tabla sobre la que se opera
     * @var string
     */
    private $_tablename = "";

    /**
     * Obtiene el tipo de dato del parámetro
     * @var string $type Tipo de dato
     */
    private function GetPropertyType($type = ""){
        if($type == "string" || $type == "date"){
            return "s";
        }
        elseif ($type == "int" || $type == "bool"){
            return "i";
        }
        elseif ($type == "double" || $type == "float"){
            return "d";
        }
        else{
            return "s";
        }
    }

    /**
     * Obtiene el tipo de dato del parámetro
     * @var string $type tipo de dato
     * @var object $val valor de dato
     */
    private function GetPropertyValue($type = "", $val= null){
        $value = $val;

        if($type == "bool"){
            $value = ($val) ? 1: 0;
        }

        return $value;
    }

    /**
     * Validar si la entidad especificada está mapeada en el
     * archivo de configuración
     * @var string $entityName Nombre de la entidad
     */
    private function IsMapped($entityName = "" ){
        // Comprobar que se ha pasado un nombre de entidad
        if($entityName == ""){
            throw new \StmtClientException("EntityName : is Empty");
        }
        // Comprobar que la entidad está definida en la lista
        // de objetos mapeados
        if(!array_key_exists( $entityName, $this->_dbObjects )){
            throw new \StmtClientException("EntityName : ".$entityName);
        }
        // Retornar datos del objeto mapeado
        return $this->_dbObjects[$entityName];
    }

    /**
     * Iniciar los atributos utilizados al construir las querys
     */
    private function InitQueryParameters(){
        // Iniciar la lista de parámetros
        $this->_parameters = array();
        // Iniciar la lista de tipos
        $this->_strTypes = "";
        // Iniciar variables para las consultas
        $this->_query = "";
        $this->_pkname = "";
        $this->_where = "";
        $this->_tablename = "";
    }

    /**
     * Obtiene los atributos del nodo xml y los devuelve en un array
     * @param object $attrs Referencia al nodo xml
     * @return array
     */
    private static function ReadAttributes($attrs = null){
        $atributos = array();
        // Recorrer la colección de columnas del xml generando el array de columnas
        foreach($attrs as $attr){
            // Obtener los atributos del nodo
            $attributes = $attr->attributes();
            // Guardar los atributos en el array
            $atributos[(string)$attributes->property] = array(
                "Name" => (string) $attributes->name,
                "Property" => (string) $attributes->property,
                "DataType" => (string) $attributes->dataType,
                "ColumnType" => (string) $attributes->columnType,
                "Required" => (string) $attributes->required,
                "MaxLength" => (isset($attributes->maxLength))
                    ? (string) $attributes->maxLength : "-",
                "Min" => (isset($attributes->min))
                ? (string) $attributes->min : "-",
                "Max" => (isset($attributes->max))
                ? (string) $attributes->max : "-"
            );
        }
        return $atributos;
    }

    /**
     * Carga la información de los atributos de un objeto de base de datos
     * @var object $object Referencia al nodo xml
     */
    private static function GetAttributes($object = null){
        // colección de columnas
        $atributos = array();
        // validar el nodo xml
        if($object == null){
            return $atributos;
        }
        // Obtener la colección de columnas de la tabla
        $attrs = $object->children();
        // Obtener los atributos
        return StmtClient::ReadAttributes($attrs);
    }

    /**
     * Carga la información de los objetos de base de datos
     * @var object $objects parámetros de carga
     */
    private function Load($objects = null){
        // Validación del parámetros
        if( $objects == null ){
            return;
        }
        // Recorremos la colección de tablas cargando los
        // datos de cada nodo xml
        foreach($objects as $object){
            // Obtener las columnas de la tabla
            $attrs = StmtClient::GetAttributes($object);

            $attributes = $object->attributes();
            // Agregar cada objeto de base de datos incluido en el xml
            $this->_dbObjects[(string)$attributes->entity] = array(
                "Type" =>(string)$attributes->type,
                "Name" => (string)$attributes->name,
                "Entity" => (string)$attributes->entity,
                "Properties" =>$attrs
            );
        }
    }

    /**
     * Obtener los datos de configuración de la conexión desde el
     * xml de descripción de la base de datos
     * @var array $databaseNode Nodo xml sobre la base de datos
     */
    private function GetDataConnection($databaseNode = null){
        // Validar el nodo pasado como argumento
        if($databaseNode == null) {
            return array();
        }
        // Extraer info de los atributos
        $attrs = $databaseNode->attributes();
        // Retornar array con los datos de conexión
        return array(
                "server" => (string)$attrs->server,
                "user" => (string)$attrs->user,
                "password" => (string)$attrs->password,
                "scheme" => (string)$attrs->scheme
         );
    }

    /**
     * Carga la información del fichero de configuración
     */
    private function LoadDataBase(){
        // Validar el fichero de descripción de base de datos
        if(!file_exists( $this->_filename )){
            throw new \Exception( "FileNotFound :".$this->_filename);
        }
        // Leer el xml con la descripción de la base de datos
        $configurator = simplexml_load_file($this->_filename);
        // Setear los datos de configuración de la conexión a base de datos
        $this->_oConnData =
                $this->GetDataConnection($configurator->database);
        // Extraer la colección de objetos de la base de datos
        $objects = $configurator->objects->children();
        // Cargar la colección de objetos
        $this->Load($objects);
    }

    /**
     * Constructor privado de la clase
     * @var string $fileName Nombre del fichero
     */
    private function __construct($fileName = ""){
        // Setear el fichero de configuración
        $this->_filename = ($fileName != "")
                ? $fileName : "database.xml";
        // Cargar la información de base de datos
        $this->LoadDataBase();
    }

    /**
     * Destructor de la clase
     */
    public function __destruct(){
        $this->Close();
    }

    /**
     * Adapta los datos obtenidos contra una entidad
     * @var string $entityName Nombre de la entidad
     * @var array $arrayData datos
     */
    public function SetEntity($entityName, $arrayData){
        // Instanciar entidad
        $entity = new $entityName();
        // Instanciar reflector
        $reflector = new \ReflectionClass($entityName);
        // Obtener las propiedades del objeto
        $properties = $reflector->getProperties();
        // Recorrer las propiedades asignando el valor correspondiente
        foreach($properties as $property){
            // Validar la propiedad con el array
            if(!array_key_exists($property->getName(), $arrayData)){
                continue;
            }
            // Setear el valor de la propiedad
            $entity->{ $property->getName() } =
                    $arrayData[$property->getName()];
        }
        // Retornar instancia de la entidad
        return $entity;
    }

    /**
     * Obtener una instancia de entidad con el Id seteado
     * @var string $entityName Nombre de la entidad
     * @var object $identity Identidad de la entidad
     */
    public function GetEntity($entityName, $identity){
        // Comprobar que se ha definido la entidad
        $object = $this->IsMapped($entityName);
        // Obtener el nombre del ojeto
        $this->_tablename = $object["Name"];
        // Obtener las columnas
        $columns = $object["Properties"];
        // Instanciar entidad
        $entity = new $entityName();
        // Asignar el valor de la identidad
        // foreach($columns as $key => $value){
        foreach($columns as $value){
            if($value["ColumnType"] == "pk"
                    || $value["ColumnType"] == "pk-auto"){
                $entity-> { $value["Property"] } = $identity;
            }
        }
        // Retornar referencia a la entidad
        return $entity;
    }

    /**
     * Agrega el tipo de dato de la propiedad filtrando si no es
     * un "filter"
     * @param array $data Información de la propiedad
     */
    private function SetPropertyType($data = ""){
        $pos = strpos($data["Property"], "-filter");
        // Parámetros por filtro
        if(!($pos === false)){
            $this->_strTypes .= $this->GetPropertyType( $data["DataType"] );
        }
    }

    /**
     * Establece los parámetros de consulta
     * @var object $entity Referencia a la entidad
     */
    public function SetParameters($entity = null){
        // Validar parámetro
        if($entity != null){
            if(!is_array($entity)){
                settype( $entity, "array" );
            }
            foreach($this->_parameters as $key => $value){

                if(array_key_exists($value["Property"], $entity)) {
                    //$propName = $value["Property"];
                    $this->_parameters[$key]["Value"] =
                            $this->GetPropertyValue($value["DataType"],
                                    $entity[$value["Property"]]);
                    $this->_strTypes .=
                            $this->GetPropertyType($value["DataType"]);
                    continue;
                }

                $this->SetPropertyType($value);
                /*
                $pos = strpos($value["Property"], "-filter");
                    // Parámetros por filtro
                if(!($pos === false)){
                    $this->_strTypes .=
                            $this->GetPropertyType( $value["DataType"] );
                }
                */
            }
        }
    }

    /**
     * Ejecuta una consulta sin evaluar el resultado
     * @var string $query Consulta Sql a ejecutar
     */
    public function Execute($query){
        $stmt = $this->_oConn->prepare($query);
        $parameters = array();
        $parameters[] = $this->_strTypes;
        foreach($this->_parameters as $key => $value){
            $parameters[] = &$this->_parameters[$key]["Value"];
        }

        call_user_func_array(array($stmt, 'bind_param'), $parameters);

        if($stmt){
            if($stmt->execute()) {
                $id = $stmt->insert_id;
                $stmt->close();
                return $id;
            }
            throw new \StmtClientExecuteException(
                "Execute execute fail : ".$stmt->error);
        }
        throw new \StmtClientExecuteException(
            "Execute prepare fail : ".$this->_oConn->error);
    }

    /**
     * Ejecuta una consulta de lectura de datos
     * @var string $query Consulta Sql a ejecutar
     * @var string $entityName Nombre de la entidad
     */
    public function ExecuteQuery($query, $entityName){
        $result = array();
        $temp = new $entityName();
        settype( $temp, "array" );

        if (true == $stmt = $this->_oConn->prepare($query)) {
            $count = count($this->_parameters);
            if( $count > 0){
                $parameters[] = $this->_strTypes;
                foreach($this->_parameters as $key => $value){
                    $parameters[] = &$this->_parameters[$key]["Value"];
                }

                call_user_func_array(array($stmt, 'bind_param'),
                        $parameters);
            }

            if($stmt->execute()){
                $parameters = array();
                foreach($temp as $key => $value){
                    if(is_array($value)) {
                        continue;
                    }
                    $parameters[$key] = &$temp[$key];
                }

                call_user_func_array(array($stmt, 'bind_result'),
                        $parameters);

                while ($stmt->fetch()) {
                    $item = $this->SetEntity($entityName, $temp);
                    array_push($result, $item);
                }
                $stmt->close();
            }
            else{
                throw new \StmtClientExecuteException(
                        "ExecuteQuery execute fail : ".$stmt->error);
            }
        }
        else{
            throw new \StmtClientExecuteException(
                    "ExecuteQuery prepare fail : ".$this->_oConn->error);
        }

        return $result;
    }

    /**
     * Genera el string de consulta para la obtener/leer una
     * entidad filtrada por su id
     * @var string $entityName Nombre de la entidad
     */
    public function GetReadQuery($entityName = ""){
        // Iniciar los parámetros
        $this->InitQueryParameters();
        // Comprobar que se ha definido la entidad
        $object = $this->IsMapped($entityName);
        // Obtener nombre de la tabla
        $this->_tablename = $object["Name"];
        // Obtener las columnas
        $columns = $object["Properties"];
        // foreach($columns as $key => $value){
        foreach($columns as $value){
            $this->_query .= ", ".$value["Name"]." as ".$value["Property"];
            if($value["ColumnType"] == "pk"
                    || $value["ColumnType"] == "pk-auto"){
                $this->_pkname = $value["Name"];
                array_push($this->_parameters, array (
                    "Name" => $value["Name"],
                    "Property" => $value["Property"],
                    "DataType" => $value["DataType"],
                    "Value" => ""
                ));
            }
        }

        $this->_query = substr($this->_query, 1);

        return "SELECT ".$this->_query." FROM "
                .$this->_tablename." WHERE ".$this->_pkname." = ? ;";
    }

    /**
     * Obtiene la cadena de columnas y parámetros para una consulta
     * Insert
     * @param array $columns Colección de columnas que intervienen
     * @return array
     */
    private function GetParamsAndNamesInsert($columns = null){

        $result = array( "Names" => "", "Params" => "" );

        if(isset($columns) && is_array($columns)){
            $sParams = "";
            $sNames = "";
            // foreach($columns as $key => $value){
            foreach($columns as $value){
                if($value["ColumnType"] != "pk-auto"){
                    $sNames .= ", ".$value["Name"];
                    $sParams .= ", ?";
                    array_push($this->_parameters, array (
                                "Name" => $value["Name"],
                                "Property" => $value["Property"],
                                "DataType" => $value["DataType"],
                                "Value" => ""
                        ));
                }
            }
            $result["Names"] = substr($sNames, 1);
            $result["Params"] = substr($sParams, 1);
        }
        return $result;
    }

    /**
     * Genera el string de consulta para la creación una entidad
     * @var string $entityName Nombre de la entidad
     */
    public function GetCreateQuery($entityName = ""){
        // Iniciar los parámetros
        $this->InitQueryParameters();
        // Comprobar que se ha definido la entidad
        $object = $this->IsMapped($entityName);
        // Obtener nombre de la tabla
        $this->_tablename = $object["Name"];
        // Obtener las columnas
        $columns = $object["Properties"];
        // obtiene las subcadenas de la consulta
        $res = $this->GetParamsAndNamesInsert($columns);
        // Retornar la consulta
        return "INSERT INTO ".$this->_tablename
            ." (".$res["Names"].") VALUES (".$res["Params"].");";
    }

    /**
     * Genera el string de consulta para la actualización una entidad
     * @var string $entityName Nombre de la entidad
     */
    public function GetUpdateQuery($entityName = ""){
        // Iniciar los parámetros
        $this->InitQueryParameters();
        // Comprobar que se ha definido la entidad
        $object = $this->IsMapped($entityName);
        // Obtener nombre de la tabla
        $this->_tablename = $object["Name"];
        // Obtener las columnas
        $columns = $object["Properties"];

        $pkName = "";
        $pkProperty = "";
        $pkDataType = "";

        foreach($columns as $key => $value){
            // Si es clave, guardamos los datos para
            // incluirlos en el filtro where
            if($value["ColumnType"] == "pk-auto"
                    || $value["ColumnType"] == "pk"){
                $pkName = $value["Name"];
                $pkProperty = $value["Property"];
                $pkDataType = $value["DataType"];
                continue;
            }

            $this->_query .= ", ".$value["Name"]." = ?";

            array_push($this->_parameters, array (
                                "Name" => $value["Name"],
                                "Property" => $value["Property"],
                                "DataType" => $value["DataType"],
                                "Value" => ""
                            ));
        }

        array_push($this->_parameters, array (
                            "Name" => $pkName,
                            "Property" => $pkProperty,
                            "DataType" => $pkDataType,
                            "Value" => ""
                        ));

        $this->_query = substr($this->_query, 1);

        return "UPDATE ".$this->_tablename." SET "
                .$this->_query." WHERE ".$pkName." = ?;";
    }

    /**
     * Genera el string de consulta para eliminar una entidad por su id
     * @var string $entityName Nombre de la entidad
     */
    public function GetDeleteQuery($entityName = ""){
        // Iniciar los parámetros
        $this->InitQueryParameters();
        // Comprobar que se ha definido la entidad
        $object = $this->IsMapped($entityName);
        // Obtener el nombre de la tabla
        $this->_tablename = $object["Name"];
        // Obtener las columnas
        $columns = $object["Properties"];

        foreach($columns as $key => $value){
            if($value["ColumnType"] == "pk"
                    || $value["ColumnType"] == "pk-auto"){
                $this->pkname = $value["Name"];
                array_push($this->_parameters, array (
                    "Name" => $value["Name"],
                    "Property" => $value["Property"],
                    "DataType" => $value["DataType"],
                    "Value" => ""
                ));
            }
        }
        return "DELETE FROM ".$this->_tablename
                ." WHERE ".$this->pkname." = ?;";
    }

    /**
     * Genera el string de consulta para obtener una lista
     * de entidades ordenadas
     * @var string $entityName Nombre de la entidad
     * @var object $order
     */
    public function GetOrderByQuery($entityName = "", $order = null){
        // Comprobar el mapeado
        $object = $this->IsMapped($entityName);
        // Obtener las "keys" del array asociativo para crear la query
        $keys = array_keys ($order);
        // Obtener la consulta de filtro
        $sSqlQuery = $this->GetListQuery($entityName);
        // Eliminar fin de consulta
        $sqlQuery = str_replace( ";", "", $sSqlQuery );
        // Obtener los parámetros de la clausula
        $columnas = $object["Properties"];
        // Nombre de la columna
        $columna = $columnas[$keys[0]]["Name"];
        // Tipo de orden : ASC | DESC
        $tipo = $order[$keys[0]];
        // Agregar clausula order by
        $sqlQuery .= " ORDER BY ".$columna." ".$tipo;
        // retornar la consulta generada
        return $sqlQuery;
    }

    /**
     * Genera el string de consulta para obtener una lista de
     * entidades filtradas y ordenadas
     * @var string $entityName Nombre de la entidad
     * @var array $filter filtro de búsqueda
     * @var object $order
     */
    public function GetOrderByFilterQuery($entityName = "",
            $filter = null, $order = null){
        // Comprobar el mapeado
        $object = $this->IsMapped($entityName);
        // Obtener las "keys" del array asociativo para crear la query
        $keys = array_keys ($order);
        // Obtener la consulta de filtro
        $sSqlQuery = $this->GetFilterQuery($entityName, $filter);
        // Eliminar fin de consulta
        $sqlQuery = str_replace( ";", "", $sSqlQuery );
        // Obtener los parámetros de la clausula
        $columnas = $object["Properties"];
        // Nombre de la columna
        $columna = $columnas[$keys[0]]["Name"];
        // Tipo de orden : ASC | DESC
        $tipo = $order[$keys[0]];
        // Agregar clausula order by
        $sqlQuery .= " ORDER BY ".$columna." ".$tipo;
        // retornar la consulta generada
        return $sqlQuery;
    }

    /**
     * Genera el string de consulta para obtener una lista de
     * entidades filtradas
     * @var string $entityName Nombre de la entidad
     * @var array $filter Filtro de parámetros
     */
    public function GetFilterQuery($entityName = "", $filter = null){
        // Iniciar los parámetros
        $this->InitQueryParameters();
        // Comprobar que se ha definido la entidad
        $object = $this->IsMapped($entityName);
        // Obtener el nombre de la tabla
        $this->_tablename = $object["Name"];
        // Obtener las columnas
        $columns = $object["Properties"];

        foreach($columns as $key => $value){
            $this->_query .= ", ".$value["Name"]." as ".$value["Property"];
        }

        if(is_array($filter)){

            $nFilter = array();
            // traducir los nombres de columnas
            foreach($columns as $key => $value){
                if(array_key_exists( $value["Property"], $filter)){
                    $value["Value"] = $filter[$value["Property"]];
                    $nFilter[$value["Name"]] = $value;
                }
            }

            foreach($nFilter as $key => $value){

                if($value["Value"] === null){
                    $this->_where .= " AND ".$key." is null";
                    continue;
                }

                $this->_where .= ($value["DataType"]=="string"
                        || $value["DataType"]=="date")
                        ? " AND ".$key." LIKE ?" : " AND ".$key." = ?";

                array_push($this->_parameters, array (
                        "Name" => $key,
                        "Property" => $value["Property"]."-filter",
                        "DataType" => $value["DataType"],
                        "Value" => $value["Value"]
                    ));
            }

            if(strlen($this->_where) > 0){
                $this->_where = " WHERE ".substr($this->_where, 4);
            }
        }

        $this->_query = substr($this->_query, 1);

        return "SELECT ".$this->_query." FROM "
                .$this->_tablename." ".$this->_where.";";
    }

    /**
     * Genera el string de consulta para obtener una lista de
     * entidades filtradas
     * @var string $entityName Nombre de la entidad
     * @var array $filter filtro para la busqueda
     */
    public function GetStringFilterQuery($entityName = "", $filter = ""){
        // Iniciar los parámetros
        $this->InitQueryParameters();
        // Comprobar que se ha definido la entidad
        $object = $this->IsMapped($entityName);
        // Obtener nombre de la tabla
        $this->_tablename = $object["Name"];
        // Obtener las columnas
        $columns =$object["Properties"];
        //foreach($columns as $key => $value){
        foreach($columns as $value){
            $this->_query .= ", ".$value["Name"]." as ".$value["Property"];
        }

        if(strlen($filter) > 0){
            $this->_where = " WHERE ".$filter;
        }

        $this->_query = substr($this->_query, 1);

        return "SELECT ".$this->_query." FROM "
                .$this->_tablename." ".$this->_where.";";
    }

    /**
     * Genera el string de consulta para obtener una lista de entidades
     * @var string $entityName Nombre de la entidad
     */
    public function GetListQuery($entityName = ""){
        // Iniciar los parámetros
        $this->InitQueryParameters();
        // Comprobar que se ha definido la entidad
        $object = $this->IsMapped($entityName);
        // Obtener nombre de la tabla
        $this->_tablename = $object["Name"];
        // Obtener las columnas
        $columns = $object["Properties"];
        //foreach($columns as $key => $value){
        foreach($columns as $value){
            $this->_query .= ", ".$value["Name"]." as ".$value["Property"];
        }

        $this->_query = substr($this->_query, 1);

        return "SELECT ".$this->_query." FROM ".$this->_tablename.";";
    }

    /**
     * Establece los parámetros de conexión con la bbdd :
     * array ( "server" => "server", "user" => "user",
     * "password" => "password", "scheme" => "scheme");
     * @var array $data Datos de la conexión
     */
    public function SetDataConnection($data = null){
        // Validación del parámetro de conexión
        if($data == null){
            return;
        }
        // Setear los datos
        $this->_oConnData = $data;
    }

    /**
     * Abre una conexión a base de datos
     */
    public function Open(){
        // Validar que se han seteado los parámetros de conexión
        if($this->_oConnData == null){
            throw new \StmtClientException('No data connection');
        }
        // Referencia a los datos de conexión
        $data = $this->_oConnData;
        // Instanciar referencia a mysqli
        $this->_oConn = new \mysqli($data["server"], $data["user"],
                $data["password"],$data["scheme"]);
        // Comprobar que no hay errores de conexión y finalizar
        if (is_null(mysqli_connect_error())){
            return;
        }
        // Se ha producido un error: Eliminar la conexión
        // die("");
        // Lanzar una excepción con los datos del error
        throw new \StmtClientException('Fail connection.. :'
                . mysqli_connect_error());
    }

    /**
     * Cierra la conexión actual si está abierta
     */
    public function Close(){

    }

    /**
     * Obtiene una instancia del objeto de acceso a base de datos
     * @var string $fileName Nombre del fichero de configuración
     */
    public static function GetInstance($fileName = ""){
        // Comprobar si ya está instanciada la referencia
        if(StmtClient::$_singleton == null){
            // Crear una nueva instancia
            StmtClient::$_singleton = new \StmtClient($fileName);
        }
        // Retornar referencia a la instancia
        return StmtClient::$_singleton;
    }
}
