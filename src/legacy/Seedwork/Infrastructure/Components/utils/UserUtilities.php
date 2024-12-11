<?php



/*
 *  Dependencias:
 *
 *	- ConfigurationManager
 *	- Injector
 *	- PasswordFactory
 *	- IDataAccessObject y una implementación
 *	- Notification (entidad bbdd)
 */

/**
 * DTO para el envío de notificaciones de passwords
 *
 * @author alfonso
 */
class UserDTOUtils{

    /**
     * Email del usuario
     * @var string
     */
    public $Email = "";

    /**
     * Nueva contraseña generada
     * @var string
     */
    public $Password = "";

    /**
     * Fecha en la que se genera la contraseña
     * @var string
     */
    public $Date = "";

}

/**
 * Utilidades comunes para los usuarios
 *
 * @author alfonso
 */
class UserUtilities{

    /**
     * Obtiene una instancia de acceso a datos
     * @return \IDataAccessObject
     */
    private static function GetDao(){
        // Obtener referencia al inyector
        $injector = Injector::GetInstance();
        // Obtener referencia al dao
        $dao = $injector->Resolve( "IDataAccessObject" );
        // Obtener nombre de la cadena de conexión
        $connectionString =
                ConfigurationManager::GetKey( "connectionString" );
        // Obtener parámetros de conexión
        $oConnString =
                ConfigurationManager::GetConnectionStr($connectionString);
        // Configurar el objeto de conexión a datos
        $dao->Configure($oConnString);
        // Retornar referencia
        return $dao;
    }

    /**
     * Registrar los datos de usuario en la base de datos
     * @param \PasswordFactory $factory referencia al generador de passwords
     * @param \User $user Referencia a los datos de usuario
     * @return string
     */
    private static function Create($factory = null, $user = null){
        if($factory != null && $user != null){
            // Obtener referencia al dao
            $dao = UserUtilities::GetDao();
            // Generar password nueva
            $pass = $factory->GetPassword();
            // Generar Hash
            $hash = $factory->GetSHA512( $pass );
            // asignar nueva pass
            $user->Password = $hash;
            // guardar los datos
            $dao->Create( $user );
        }
        return $pass;
    }

    /**
     * Resetea el password del usuario
     * @param \PasswordFactory $factory Referencia al generador de passwords
     * @param \User $user Referencia al usuario
     * @return string
     */
    private static function Update($factory = null, $user = null){
        if($factory != null && $user != null){
            // Obtener referencia al dao
            $dao = UserUtilities::GetDao();
            // Generar password nueva
            $pass = $factory->GetPassword();
            // Generar Hash
            $hash = $factory->GetSHA512( $pass );
            // asignar nueva pass
            $user->Password = $hash;
            // guardar los datos
            $dao->Update( $user );
        }
        return $pass;
    }

    /**
     * Obtiene un dto de usuario para la notificación
     * @param \User $user
     * @param string $pass
     * @return \UserDTOUtils
     */
    private static function GetUserDto($user = null, $pass = ""){
        // Instanciar datetime
        $date = new \DateTime( "NOW" );
        // Crear dto con la información a enviar
        $userDto = new \UserDTOUtils();
        $userDto->Email = $user->Username;
        $userDto->Password = $pass;
        $userDto->Date = $date->format( "d-m-Y" );
        return $userDto;
    }

    /**
     * Genera la notificación de nuevo usuario
     * @param array $data Array con la información del contexto
     * @param \User Referencia al usuario
     * @param \UserDTOUtils Referencia al dto de usuario
     */
    private static function CreateNotification($data = null,
            $user = null, $userDto = null){
        // Instanciar datetime
        $date = new \DateTime( "NOW" );
        // Obtener referencia al dao
        $dao = UserUtilities::GetDao();
        // Crear instancia de notificación e iniciar valores
        $dto = new \Notification();
        $dto->Project = $data[ "project" ];
        $dto->Service = $data[ "service" ];
        $dto->To = $user->Username;
        $dto->Subject =  "create-user";
        $dto->Content = json_encode($userDto);
        $dto->Date = $date->format( "Y-m-d" );
        // Crear registro
        $dao->Create( $dto );
    }

    /**
     * Genera la notificación de nuevo usuario
     * @param array $data Array con la información del contexto
     * @param \User Referencia al usuario
     * @param \UserDTOUtils Referencia al dto de usuario
     */
    private static function ResetNotification($data = null,
            $user = null, $userDto = null){
        // Instanciar datetime
        $date = new \DateTime( "NOW" );
        // Obtener referencia al dao
        $dao = UserUtilities::GetDao();
        // Crear instancia de notificación e iniciar valores
        $dto = new \Notification();
        $dto->Project = $data[ "project" ];
        $dto->Service = $data[ "service" ];
        $dto->To = $user->Username;
        $dto->Subject =  "create-user";
        $dto->Content = json_encode($userDto);
        $dto->Date = $date->format( "Y-m-d" );
        // Crear registro
        $dao->Create( $dto );
    }

    /**
     * Carga la colección de usuarios que contienen el e-mail indicado
     * @param string $email
     * @return array
     */
    private static function GetUserByEmail($email = ""){
        // Obtener referencia al dao
        $dao = UserUtilities::GetDao();
        // buscar usuarios por email
        return $dao->GetByFilter( "User" , array( "Username" => $email ));
    }

    /**
     * Crea un nuevo usuario con los datos pasados como argumento
     * @param array $data data( "user" => object , "service" => "",
     * "project" => "")
     * @return boolean
     */
    public static function CreateUser($data = null){
        // Resultado por defecto
        $result = false;
        // Obtener referencia al gestor de passwords
        $factory = PasswordFactory::GetInstance();
        // Validar datos
        if(isset($data)
            && $data != null
                && is_array($data)
                    && isset($data["user"])
                        && is_object($data["user"])){
            // Obtener referencia a los datos de usuario
            $user = $data["user"];
            // Crear usuario en bbdd
            $pass = UserUtilities::Create($factory, $user);
            // Obtener dto para la notificación
            $userDto = UserUtilities::GetUserDto($user, $pass);
            // Crear notificacion
            UserUtilities::CreateNotification($data, $userDto);
            // Asignar el resultado de la operación
            $result = true;
        }
        return $result;
    }

    /**
     * Resetear la contraseña de acceso del usuario
     * @param array $data data( "email" => "" , "service" => "", "project" => "")
     * @return boolean
     */
    public static function ResetPassword($data = null){
        // Resultado por defecto
        $result = false;
        // Obtener referencia al gestor de passwords
        $factory = PasswordFactory::GetInstance();
        // buscar usuarios por email
        $emails = UserUtilities::GetUserByEmail($data["email"]);
        // Validar datos
        if(isset($emails) && $emails != null && count($emails) > 0){
            // Obtener referencia inicial
            $user = $emails[0];
            // Actualización del password de usuario
            $pass = UserUtilities::Update($factory, $user);
            // Obtener dto para la notificación
            $userDto = UserUtilities::GetUserDto($user, $pass);
            // Generar notificación
            UserUtilities::ResetNotification($data, $user, $userDto);
            // Asignar el resultado de la operación
            $result = true;
        }

        return $result;
    }

    /**
     * Modificar la contraseña acceso
     * @param array $data aray( "email" => "" , "pass" => "" , "newpass" => "")
     * @return boolean
     */
    public static function ChangePassword($data = null){
        // Resultado por defecto
        $result = false;
        // Obtener referencia al dao
        $dao = UserUtilities::GetDao();
        //$dao = $injector->Resolve( "IDataAccessObject" );
        // Filtro de búsqueda
        $filter = array( "Username" => $data[ "email" ],
            "Password" => $data[ "pass" ] );
        // buscar usuarios por email
        $emails = $dao->GetByFilter( "User" , $filter);
        // Validar datos
        if(isset($emails) && $emails != null && count($emails) > 0){
            // Obtener referencia inicial
            $user = $emails[0];
            // Generar password nueva
            $hash = $data[ "newpass" ];
            // asignar nueva pass
            $user->Password = $hash;
            // guardar los datos
            $dao->Update( $user );
            // Asignar el resultado de la operación
            $result = true;
        }
        return $result;
    }

}
