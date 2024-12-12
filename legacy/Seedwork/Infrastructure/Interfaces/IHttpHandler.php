<?php

declare(strict_types=1);

/**
 * Interfaz para el manejador de peticiones http en contextos MVC
 *
 * @author alfonso
 */
interface IHttpHandler{

    /**
     * Determina si el nombre del controlador pasado como argumento se
     * corresponde con un controlador válido (existe).
     * @param string $controller Nombre del controlador a validar
     */
    public function ValidateController($controller);

    /**
     * Determina si el nombre del controlador pasado como argumento se
     * corresponde con un controlador válido y si existe alguna acción
     * en dicho controlador con el nombre pasado como argumento.
     * @param string $controller Nombre del controlador
     * @param string $action Nombre de la acción
     */
    public function Validate($controller, $action);

    /**
     *  Establece el controlador y la acción por defecto si procede
     * @param string $controller Nombre del controlador
     * @param string $action Nombre de la acción
     */
    public function SetDefault($controller, $action);

    /**
     * Procesa la url actual obteniendo el nombre del controlador, la
     * acción y los parámetros de la petición realizada. La información
     * obtenida se retornará mediante un array/diccionario donde se
     * especifique cada uno de las partes obtenidas. En el caso de que la
     * url no esté correctamente formada se lanzará una excepción.
     * @param string $urlRequest Url a procesar
     */
    public function ProcessUrl($urlRequest);

    /**
     * Debe encargarse de realizar el procesado de los parámetros de la
     * petición según las especificaciones del proyecto.
     * @param string $parameters parámetros pasados por url
     */
    public function ProcessParameters($parameters);

    /**
     * Se encarga de configurar el idioma de la petición/ ejecución en los
     * contextos donde es necesario(aplicaciones multilenguaje).
     * @param type $language
     */
    public function SetLanguage($language);

    /**
     * Obtiene el lenguaje del contexto actual si es que está definido
     * o una cadena vacía.
     */
    public function GetLanguage();

    /**
     * Ejecuta la acción del controlador definidos por la petición con los
     * parámetros fijados (si hay). Retorna la información que la petición
     * devolverá como respuesta a la petición del cliente.
     * @param string $controller Nombre del controlador a cargar
     * @param string $action Nombre de la acción a ejecutar
     * @param array $params Parámetros de la url obtenidos
     */
    public function Run($controller, $action, $params = null);

    /**
     * Almacena en el contexto la colección de rutas establecidas
     * (controlador-acción) para poder validar la acción y el controlador
     * que se desean ejecutar.
     * @param array $routes Colección de "rutas" disponibles
     */
    public function RegisterRoutes($routes);
}
