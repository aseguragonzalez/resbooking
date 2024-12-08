<?php

    /**
     * Entidad para el proceso de autenticación y autorización
     */
    class AuthEntity{

        /**
         * Propiedad Id del usuario
         * @var int Identidad del usuario
         */
        public $IdUser = 0;

        /**
         * Propiedad Id del servicio
         * @var int Identidad del servicio
         */
        public $IdService = 0;

        /**
         * Propiedad Id del role
         * @var int Idenidad del role
         */
        public $IdRole = 0;

        /**
         * Nombre de usuario
         * @var string Nombre de usuario
         */
        public $Username = "";

        /**
         * Password de acceso
         * @var string Password de acceso
         */
        public $Password = "";

        /**
         * Nombre del role asociado
         * @var string Nombre del Role asociado
         */
        public $Role = "";

        /**
         * Nombre del servicio asociado
         * @var string Nombre del servicio asociado
         */
        public $Service = "";

        /**
         * Id del proyecto asociado
         * @var int Identidad del proyecto
         */
        public $IdProject = 0;

    }

    /**
     * Entidad Role
     */
    class Role{

        /**
         * Propiedad Id de Role
         * @var int Identidad del Role
         */
        public $Id = 0;

        /**
         * Propiedad Name de Role
         * @var string Nombre utilizado para distinguir el role
         */
        public $Name = "";

        /**
         * Propiedad Description de Role
         * @var string Descripción funcional del role
         */
        public $Description = "";

        /**
         * Propiedad Active de Role
         * @var boolean Estado del role
         */
        public $Active = true;

    }

    /**
     * Entidad Service
     */
    class Service{

        /**
         * Propiedad Id de Service
         * @var int Identidad de Servicio
         */
        public $Id = 0;

        /**
         * Propiedad Name de Service
         * @var string Nombre del servicio
         */
        public $Name = "";

        /**
         * Propiedad Path de Service
         * @var string Ruta fisica de la aplicacion cliente
         */
        public $Path = "";

        /**
         * Propiedad Platform de Service
         * @var string Ruta de la plataforma web utilizada
         */
        public $Platform = "";

        /**
         * Propiedad Description de Service
         * @var string Descripcion funcional del servicio
         */
        public $Description = "";

        /**
         * Propiedad Active de Service
         * @var boolean Estado del servicio
         */
        public $Active = true;

    }

    /**
     * Relación entre proyectos y usuarios asociados
     */
    class ServiceRole{

        /**
         * Identidad en la tabla relacional
         * @var int Identidad del registro
         */
        public $Id = 0;

        /**
         * Identidad del servicio
         * @var int Identidad del servicio asociado
         */
        public $IdService = 0;

        /**
         * Identidad del rol
         * @var int Identidad del role asociado
         */
        public $IdRole = 0;

    }

    /**
     * Entidad User
     */
    class User{

        /**
         * Propiedad Id de User
         * @var int Identidad del usuario
         */
        public $Id = 0;

        /**
         * Propiedad Username de User
         * @var string Nombre de usuario (e-mail)
         */
        public $Username = "";

        /**
         * Propiedad Password de User
         * @var string Contraseña de acceso
         */
        public $Password = "";

        /**
         * Propiedad Active de User
         * @var boolean Estado del usuario
         */
        public $Active = true;

    }

    /**
     * Relación entre roles, servicios y usuarios asociados
     */
    class UserRoleService{

        /**
         * Identidad en la tabla relacional
         * @var int Identidad del registro
         */
        public $Id = 0;

        /**
         * Identidad del usuario
         * @var int Identidad del usuario
         */
        public $IdUser = 0;

        /**
         * Identidad del servicio
         * @var int Identidad del servicio
         */
        public $IdService = 0;

        /**
         * Identidad del role
         * @var int Identidad del role
         */
        public $IdRole = 0;

    }

    /**
     * Relación entre roles, servicios y usuarios asociados
     */
    class UserRoleServiceProject{

        /**
         * Identidad en la tabla relacional
         * @var int Identidad del registro
         */
        public $Id = 0;

        /**
         * Identidad del usuario
         * @var int Identidad del usuario
         */
        public $IdUser = 0;

        /**
         * Identidad del servicio
         * @var int Identidad del servicio
         */
        public $IdService = 0;

        /**
         * Identidad del role
         * @var int Identidad del role
         */
        public $IdRole = 0;

        /**
         * Identidad del proyecto
         * @var int Identidad del proyecto
         *
         */
        public $IdProject = 0;

    }

    /**
     * Entidad Project
     */
    class Project{

        /**
         * Propiedad Id de Project
         * @var int Identidad del proyecto
         */
        public $Id = 0;

        /**
         * Propiedad Name de Project
         * @var string Nombre de proyecto
         */
        public $Name = "";

        /**
         * Propiedad Description de Project
         * @var string Descripción del proyecto
         */
        public $Description = "";

        /**
         * Propiedad Path de Project
         * @var string Ruta de acceso al proyecto
         */
        public $Path = "";

        /**
         * Propiedad Date de Project
         * @var string Fecha de alta del proyecto
         */
        public $Date = null;

        /**
         * Propiedad Active de Project
         * @var boolean Estado lógico del proyecto
         */
        public $Active = true;

    }

    /**
     * Relación entre proyectos y servicios asociados
     */
    class ProjectServices{

        /**
         * Identidad en la tabla relacional
         * @var int Identidad del registro
         */
        public $Id = 0;

        /**
         * Identidad del proyecto
         * @var int Identidad del proyecto
         */
        public $IdProject = 0;

        /**
         * Identidad del servicio
         * @var int Identidad del servicio
         */
        public $IdService = 0;
    }

    /**
     * Relación entre proyectos y usuarios asociados
     */
    class ProjectUsers{

        /**
         * Identidad en la tabla relacional
         * @var int Identidad del registro
         */
        public $Id = 0;

        /**
         * Identidad del proyecto
         * @var int Identidad del proyecto
         */
        public $IdProject = 0;

        /**
         * Identidad del usuario
         * @var int Identidad del usuario
         */
        public $IdUser = 0;
    }

    /**
     * Entidad Notificación
     */
    class Notification{

        /**
         * Propiedad Id de la notificación
         * @var int Identidad de la notificación
         */
        public $Id = 0;

        /**
         * Propiedad Project
         * @var int proyecto asociado
         */
        public $Project = 0;

        /**
         * Propiedad Service
         * @var int servicio que genera el registro
         */
        public $Service = 0;

        /**
         * Propiedad To
         * @var string Destino de la notificación
         */
        public $To = "";

        /**
         * Propiedad Subject
         * @var string Asunto de la notificación
         */
        public $Subject = "";

        /**
         * Propiedad Header
         * @var string Cabecera del e-mail
         */
        public $Header = "";

        /**
         * Propiedad Content
         * @var string Contenido de la notificación
         */
        public $Content = "";

        /**
         * Propiedad Date
         * @var string  Fecha en la que se genera la notificación
         */
        public $Date = "";

        /**
         * Propiedad Dispatched
         * @var int  Número de veces que la notificación ha sido enviada
         */
        public $Dispatched = 0;

    }

    /**
     * DTO con la información de la notificación y su configuración
     */
    class NotificationDTO{

        /**
         * Propiedad Id ( identidad de la notificación asociada )
         * @var int Identidad del registro de notificación
         */
        public $Id = 0;

        /**
         * Propiedad Project ( proyecto asociado )
         * @var int Identidad del proyecto asociado
         */
        public $Project = 0;

        /**
         * Propiedad Service ( servicio que genera el registro )
         * @var int Identidad del servicio asociado
         */
        public $Service = 0;

        /**
         * Propiedad To ( Destino de la notificación )
         * @var string Destino de la notificación
         */
        public $_To = "";

        /**
         * Propiedad Subject ( Asunto de la notificación [clave] )
         * @var string Asunto de la notificación
         */
        public $_Subject = "";

        /**
         * Propiedad Content ( Contenido de la notificación serializado json)
         * @var string Contenido de la notificación
         */
        public $Content = "";

        /**
         * Propiedad Dispached ( Cantidad de veces que ha sido enviada
         * la notificación)
         * @var int Número de veces que se ha realizado el envío
         */
        public $Dispatched = 0;

        /**
         * Propiedad confSubject ( Asunto de la notificación [clave] )
         * @var string Asunto de la notificación
         */
        public $confSubject = "";

        /**
         * Propiedad confSubjectText ( Asunto de la notificación [texto] )
         * @var string Texto utilizado en el asunto de la notificación
         */
        public $confSubjectText = "";

        /**
         * Propiedad From ( Origen de la notificación )
         * @var string Origen de la nofiticación
         */
        public $_From = "";

        /**
         * Propiedad confTo ( Destinatario de administración )
         * @var string Destino de la notificación[Administración]
         */
        public $confTo = "";

        /**
         * Propiedad confTemplate ( plantilla  )
         * @var string Plantilla utilizada en la notificación
         */
        public $confTemplate = "";

        /**
         * Propiedad State ( Estado del servicio )
         * @var boolean Estado de la configuración
         */
        public $oConfState = 1;

    }

    /**
     * Entidad configuración de notificaciones
     */
    class NotificationConfig{

        /**
         * Propiedad Id de la notificación
         * @var int Identidad del registro de configuración
         */
        public $Id = 0;

        /**
         * Propiedad Project ( proyecto asociado )
         * @var int Identidad del proyecto asociado
         */
        public $Project = 0;

        /**
         * Propiedad Service ( servicio que genera el registro )
         * @var int Identidad del servicio asociado
         */
        public $Service = 0;

        /**
         * Propiedad Subject ( Asunto de la notificación )
         * @var string Tipificación del Asunto de la notificación
         */
        public $Subject = "";

        /**
         * Propiedad Text ( Asunto de la notificación a visualizar)
         * @var string Texto utilizado en el asunto de la notificación
         */
        public $Text = "";

        /**
         * Propiedad From ( Origen de la notificación )
         * @var string Origen de la notificación
         */
        public $From = "";

        /**
         * Propiedad To ( Destino de la notificación )
         * @var string Destino de la notificación
         */
        public $To = "";

        /**
         * Propiedad Template ( Ruta de la plantilla a utilizar )
         * @var string Plantilla a utilizar (html)
         */
        public $Template = "";

        /**
         * Propiedad State ( Estado del servicio )
         * @var boolean Estado de la notificación
         */
        public $State = 1;

    }

    /**
     * Entidad configuración de notificaciones
     */
    class NotificationConfigDTO{

        /**
         * Propiedad Id de la notificación
         * @var int identidad de la notificación
         */
        public $Id = 0;

        /**
         * Propiedad Project ( proyecto asociado )
         * @var int Identidad del proyecto asociado
         */
        public $Project = 0;

        /**
         * Propiedad ProjectName ( Nombre del proyecto asociado )
         * @var string Nombre del proyecto
         */
        public $ProjectName = "";

        /**
         * Propiedad Service ( servicio que genera el registro )
         * @var int Identidad del servicio asociado
         */
        public $Service = 0;

        /**
         * Propiedad ServiceName ( Nombre del servicio que genera el registro )
         * @var string Nombre del servicio asociado
         */
        public $ServiceName = "";

        /**
         * Propiedad Subject ( Asunto de la notificación )
         * @var string Tipología del asunto de notificación
         */
        public $Subject = "";

        /**
         * Propiedad Text ( Asunto de la notificación a visualizar)
         * @var string Texto del asunto de la notificación
         */
        public $Text = "";

        /**
         * Propiedad From ( Origen de la notificación )
         * @var string Origen de la notificación
         */
        public $From = "";

        /**
         * Propiedad To ( Destino de la notificación )
         * @var string Destino de la notificación
         */
        public $To = "";

        /**
         * Propiedad Template ( Ruta de la plantilla a utilizar )
         * @var string Plantilla utilizada en la notificación
         */
        public $Template = "";

        /**
         * Propiedad State ( Estado del servicio )
         * @var boolean Estado de la notificación
         */
        public $State = 1;

    }

    /**
     * Entidad tipo de notificación
     */
    class NotificationType{

        /**
         * Identidad del tipo de notificación
         * @var int Identidad del tipo de notificación
         */
        public $Id = 0;

        /**
         * Nombre del tipo de notificación
         * @var string Nombre del tipo
         */
        public $Name = "";

        /**
         * Descripción de la notificación
         * @var string Descripción breve sobre el uso del tipo
         */
        public $Description = "";

    } ?>
