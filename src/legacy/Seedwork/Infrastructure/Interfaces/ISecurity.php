<?php

declare(strict_types=1);

/**
 * Interfaz para el gestor de seguridad en arquitecturas MVC
 *
 * @author alfonso
 */
interface ISecurity{

    /**
     * Se encarga de realizar el proceso de autenticación del usuario que
     * accede a la aplicación mediante un ticket de acceso. En caso de ser
     * validado el ticket, se debe establecer el usuario como autenticado
     * en el contexto. Devuelve el resultado de la autenticación como un
     * valor booleano.
     * @param string $ticket ticket de sesión
     */
    public function AuthenticateTicket($ticket);

    /**
     * Se encarga de realizar el proceso de autenticación del usuario a
     * partir del nombre de usuario y el password utilizado. En el caso
     * de ser válidas las credenciales, se debe establecer el usuario
     * como autenticado en el contexto. Devuelve el resultado de la
     * autenticación como un valor booleano.
     * @param string $username Nombre de usuario
     * @param string $password Contraseña de acceso
     */
    public function Authenticate($username, $password);

    /**
     * Comprueba si la acción a ejecutar requiere que el usuario esté
     * autenticado o no
     * @param string $controller Nombre del controlador
     * @param string $action Nombre de la acción
     */
    public function RequiredAuthentication($controller, $action);

    /**
     * Determina si el usuario autenticado en el contexto tiene
     * autorización para acceder al controlador. Los criterios que
     * determinan si el usuario debe ser autorizado dependen de la
     * aplicación donde deba integrarse. Devuelve el resultado de la
     * autorización como un valor booleano.
     * @param string $controller Nombre del controlador
     */
    public function AuthorizeController($controller);

    /**
     * Determina si el usuario autenticado en el contexto tiene
     * autorización para ejecutar la acción del controlador. Los criterios
     * que determinan si el usuario debe ser autorizado dependen de la
     * aplicación donde el componente se integra. Devuelve el resultado
     * de la autorización como un valor booleano.
     * @param string $controller Nombre del controlador
     * @param string $action Nombre de la acción
     */
    public function Authorize($controller, $action);

    /**
     * Obtiene el nombre del usuario autenticado en el contexto. En caso
     * de no haber usuario autenticado, el método devolverá una
     * cadena vacía.
     */
    public function GetUserName();

    /**
     * Obtiene un array con el/los roles asociados al usuario autenticado
     * en el contexto. En caso de no estar autenticado el usuario, debe
     * retornar un array vacío.
     */
    public function GetUserRoles();

    /**
     * Obtiene un objeto con la información del usuario almacenada en el
     * contexto. En caso de no estar el usuario autenticado, se retornará
     * el valor null.
     */
    public function GetUserData();

    /**
     * Obtiene un ticket de autenticación a partir de la información del
     * usuario autenticado. En caso de no estar el usuario autenticado,
     * se retornará una cadena vacía.
     * @return string Ticket
     */
    public function GetTicket();

    /**
     * Obtiene el nombre de la vista a utilizar para la acción, el
     * controlador y el usuario autenticado. En el caso de no ser
     * necesario (no hay filtro de contenidos), retornará el nombre de
     * la vista por defecto (mismo nombre que la acción).
     * @param string $controller Nombre del controlador
     * @param string $action Nombre de la acción
     */
    public function GetViewName($controller, $action);

    /**
     * Obtiene el array de controladores disponibles para el conjunto de
     * roles pasados como parámetros.
     * @param object $roles Roles del usuario
     */
    public function GetControllersByRol($roles);
}
