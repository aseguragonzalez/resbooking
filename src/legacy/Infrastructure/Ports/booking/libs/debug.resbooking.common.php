<?php

/*
 * Copyright (C) 2015 alfonso
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Entidad Notificación
 *
 * @author alfonso
 */
class Notification{

    /**
     * Identidad de la notificación
     * @var int
     */
    public $Id = 0;

    /**
     * proyecto asociado
     * @var int
     */
    public $Project = 0;

    /**
     * Servicio que genera el registro
     * @var int
     */
    public $Service = 0;

    /**
     * Destino de la notificación
     * @var string
     */
    public $To = "";

    /**
     * Asunto de la notificación
     * @var string
     */
    public $Subject = "";

    /**
     * Cabecera del e-mail
     * @var string
     */
    public $Header = "";

    /**
     * Contenido de la notificación
     * @var string
     */
    public $Content = "";

    /**
     * Fecha en la que se genera la notificación
     * @var string
     */
    public $Date = "";

    /**
     * Número de veces que la notificación ha sido enviada
     * @var int
     */
    public $Dispatched = 0;
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

    }

/*
 * Copyright (C) 2015 alfonso
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Entidad Proyecto
 *
 * @author alfonso
 */
class Project{

    /**
     * Identidad del proyecto
     * @var int
     */
    public $Id = 0;

    /**
     * Nombre de proyecto
     * @var string
     */
    public $Name = "";

    /**
     * Descripción del proyecto
     * @var string
     */
    public $Description = "";

    /**
     * Ruta de acceso al proyecto
     * @var string
     */
    public $Path = "";

    /**
     * Fecha de alta del proyecto
     * @var string
     */
    public $Date = NULL;

    /**
     * Estado lógico del proyecto
     * @var boolean
     */
    public $Active = TRUE;
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

/*
 * Copyright (C) 2015 alfonso
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Entidad Usuario
 *
 * @author alfonso
 */
class User{

    /**
     * Identidad del usuario
     * @var int
     */
    public $Id = 0;

    /**
     * Nombre de usuario (e-mail)
     * @var string
     */
    public $Username = "";

    /**
     * Contraseña de acceso
     * @var string
     */
    public $Password = "";

    /**
     * Estado del usuario
     * @var boolean
     */
    public $Active = TRUE;
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

/*
 * Copyright (C) 2015 alfonso
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Entidad para el proceso de autenticación y autorización
 *
 * @author alfonso
 */
class AuthEntity{

    /**
     * Identidad del usuario
     * @var int
     */
    public $IdUser = 0;

    /**
     * Identidad del servicio
     * @var int
     */
    public $IdService = 0;

    /**
     * Idenidad del role
     * @var int
     */
    public $IdRole = 0;

    /**
     * Nombre de usuario
     * @var string
     */
    public $Username = "";

    /**
     * Password de acceso
     * @var string
     */
    public $Password = "";

    /**
     * Nombre del Role asociado
     * @var string
     */
    public $Role = "";

    /**
     * Nombre del servicio asociado
     * @var string
     */
    public $Service = "";

    /**
     * Identidad del proyecto
     * @var int
     */
    public $IdProject = 0;
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

/*
 * Copyright (C) 2015 alfonso
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 *  DTO con la información resumen de un proyecto
 *
 *  @author alfonso
 */
class ProjectInfo{

    /**
     * Identidad de proyecto
     * @var int
     */
    public $Id = 0;

    /**
     * Nombre de proyecto
     * @var string
     */
    public $Name = "";

    /**
     * Descripción del proyecto
     * @var string
     */
    public $Description = "";

    /**
     * Ruta física del proyecto
     * @var string
     */
    public $Path = "";

    /**
     * Fecha de alta del proyecto
     * @var string
     */
    public $Date = NULL;

    /**
     * Identidad del servicio asociado
     * @var int
     */
    public $IdService = 0;

    /**
     * Identidad del usuario asociado
     * @var int
     */
    public $IdUser = 0;

    /**
     * Nombre del usuario asociado
     * @var string
     */
    public $Username = "";

    /**
     * Estado actual del proyecto
     * @var boolean
     */
    public $Active = TRUE;
} ?>
