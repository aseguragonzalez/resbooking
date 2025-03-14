# Design decisions

## RequestBuilder rules

1. Arguments are going to be normalized to lowercase when compare with request params.

2. Arguments could come from HttpMessage using different cases:
    - CamelCase (default): `id=1&myName=John`
    - PascalCase: `Id=1&MyName=John`
    - SnakeCase: `id=1&my_name=John`
    - KebabCase: `id=1&my-name=John`

3. Arguments could use different symbols to indicate levels in the request params:
    - Dot (default): `id=1&person.name=John`
    - Other: custom separators could be used, but the default is the dot.
