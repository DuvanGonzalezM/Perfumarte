<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Líneas de Lenguaje de Validación
    |--------------------------------------------------------------------------
    |
    | Las siguientes líneas de lenguaje contienen los mensajes de error predeterminados
    | utilizados por la clase validadora. Algunas de estas reglas tienen múltiples
    | versiones como las reglas de tamaño. Siéntase libre de ajustar cada uno de estos
    | mensajes aquí.
    |
    */
    'accepted' => 'El campo :attribute debe ser aceptado.',
    'accepted_if' => 'El campo :attribute debe ser aceptado cuando :other es :value.',
    'active_url' => 'El campo :attribute debe ser una URL válida.',
    'after' => 'El campo :attribute debe ser una fecha posterior a :date.',
    'after_or_equal' => 'El campo :attribute debe ser una fecha posterior o igual a :date.',
    'alpha' => 'El campo :attribute solo debe contener letras.',
    'alpha_dash' => 'El campo :attribute solo debe contener letras, números, guiones y guiones bajos.',
    'alpha_num' => 'El campo :attribute solo debe contener letras y números.',
    'array' => 'El campo :attribute debe ser un arreglo.',
    'ascii' => 'El campo :attribute solo debe contener caracteres alfanuméricos y símbolos de un solo byte.',
    'before' => 'El campo :attribute debe ser una fecha anterior a :date.',
    'before_or_equal' => 'El campo :attribute debe ser una fecha anterior o igual a :date.',
    'between' => [
        'array' => 'El campo :attribute debe tener entre :min y :max elementos.',
        'file' => 'El campo :attribute debe estar entre :min y :max kilobytes.',
        'numeric' => 'El campo :attribute debe estar entre :min y :max.',
        'string' => 'El campo :attribute debe tener entre :min y :max caracteres.',
    ],
    'boolean' => 'El campo :attribute debe ser verdadero o falso.',
    'can' => 'El campo :attribute contiene un valor no autorizado.',
    'confirmed' => 'La confirmación del campo :attribute no coincide.',
    'current_password' => 'La contraseña es incorrecta.',
    'date' => 'El campo :attribute debe ser una fecha válida.',
    'date_equals' => 'El campo :attribute debe ser una fecha igual a :date.',
    'date_format' => 'El campo :attribute debe coincidir con el formato :format.',
    'decimal' => 'El campo :attribute debe tener :decimal lugares decimales.',
    'declined' => 'El campo :attribute debe ser rechazado.',
    'declined_if' => 'El campo :attribute debe ser rechazado cuando :other es :value.',
    'different' => 'El campo :attribute y :other deben ser diferentes.',
    'digits' => 'El campo :attribute debe tener :digits dígitos.',
    'digits_between' => 'El campo :attribute debe tener entre :min y :max dígitos.',
    'dimensions' => 'El campo :attribute tiene dimensiones de imagen no válidas.',
    'distinct' => 'El campo :attribute tiene un valor duplicado.',
    'doesnt_end_with' => 'El campo :attribute no debe terminar con uno de los siguientes: :values.',
    'doesnt_start_with' => 'El campo :attribute no debe comenzar con uno de los siguientes: :values.',
    'email' => 'El campo :attribute debe ser una dirección de correo válida.',
    'ends_with' => 'El campo :attribute debe terminar con uno de los siguientes: :values.',
    'enum' => 'El campo :attribute seleccionado no es válido.',
    'exists' => 'El campo :attribute seleccionado no es válido.',
    'extensions' => 'El campo :attribute debe tener una de las siguientes extensiones: :values.',
    'file' => 'El campo :attribute debe ser un archivo.',
    'filled' => 'El campo :attribute debe tener un valor.',
    'gt' => [
        'array' => 'El campo :attribute debe tener más de :value elementos.',
        'file' => 'El campo :attribute debe ser mayor que :value kilobytes.',
        'numeric' => 'El campo :attribute debe ser mayor que :value.',
        'string' => 'El campo :attribute debe tener más de :value caracteres.',
    ],
    'gte' => [
        'array' => 'El campo :attribute debe tener :value elementos o más.',
        'file' => 'El campo :attribute debe ser mayor o igual que :value kilobytes.',
        'numeric' => 'El campo :attribute debe ser mayor o igual que :value.',
        'string' => 'El campo :attribute debe tener :value caracteres o más.',
    ],
    'hex_color' => 'El campo :attribute debe ser un color hexadecimal válido.',
    'image' => 'El campo :attribute debe ser una imagen.',
    'in' => 'El campo :attribute seleccionado no es válido.',
    'in_array' => 'El campo :attribute debe existir en :other.',
    'integer' => 'El campo :attribute debe ser un número entero.',
    'ip' => 'El campo :attribute debe ser una dirección IP válida.',
    'ipv4' => 'El campo :attribute debe ser una dirección IPv4 válida.',
    'ipv6' => 'El campo :attribute debe ser una dirección IPv6 válida.',
    'json' => 'El campo :attribute debe ser una cadena JSON válida.',
    'lowercase' => 'El campo :attribute debe estar en minúsculas.',
    'lt' => [
        'array' => 'El campo :attribute debe tener menos de :value elementos.',
        'file' => 'El campo :attribute debe ser menor que :value kilobytes.',
        'numeric' => 'El campo :attribute debe ser menor que :value.',
        'string' => 'El campo :attribute debe tener menos de :value caracteres.',
    ],
    'lte' => [
        'array' => 'El campo :attribute no debe tener más de :value elementos.',
        'file' => 'El campo :attribute debe ser menor o igual que :value kilobytes.',
        'numeric' => 'El campo :attribute debe ser menor o igual que :value.',
        'string' => 'El campo :attribute debe tener :value caracteres o menos.',
    ],
    'mac_address' => 'El campo :attribute debe ser una dirección MAC válida.',
    'max' => [
        'array' => 'El campo :attribute no debe tener más de :max elementos.',
        'file' => 'El campo :attribute no debe ser mayor que :max kilobytes.',
        'numeric' => 'El campo :attribute no debe ser mayor que :max.',
        'string' => 'El campo :attribute no debe ser mayor que :max caracteres.',
    ],
    'max_digits' => 'El campo :attribute no debe tener más de :max dígitos.',
    'mimes' => 'El campo :attribute debe ser un archivo de tipo: :values.',
    'mimetypes' => 'El campo :attribute debe ser un archivo de tipo: :values.',
    'min' => [
        'array' => 'El campo :attribute debe tener al menos :min elementos.',
        'file' => 'El campo :attribute debe ser al menos de :min kilobytes.',
        'numeric' => 'El campo :attribute debe ser al menos de :min.',
        'string' => 'El campo :attribute debe tener al menos :min caracteres.',
    ],
    'min_digits' => 'El campo :attribute debe tener al menos :min dígitos.',
    'missing' => 'El campo :attribute debe estar ausente.',
    'missing_if' => 'El campo :attribute debe estar ausente cuando :other es :value.',
    'missing_unless' => 'El campo :attribute debe estar ausente a menos que :other sea :value.',
    'missing_with' => 'El campo :attribute debe estar ausente cuando :values esté presente.',
    'missing_with_all' => 'El campo :attribute debe estar ausente cuando :values estén presentes.',
    'multiple_of' => 'El campo :attribute debe ser un múltiplo de :value.',
    'not_in' => 'El campo :attribute seleccionado no es válido.',
    'not_regex' => 'El formato del campo :attribute no es válido.',
    'numeric' => 'El campo :attribute debe ser un número.',
    'password' => [
        'letters' => 'El campo :attribute debe contener al menos una letra.',
        'mixed' => 'El campo :attribute debe contener al menos una letra mayúscula y una minúscula.',
        'numbers' => 'El campo :attribute debe contener al menos un número.',
        'symbols' => 'El campo :attribute debe contener al menos un símbolo.',
        'uncompromised' => 'El campo :attribute ha aparecido en una fuga de datos. Por favor elija otro :attribute.',
    ],
    'present' => 'El campo :attribute debe estar presente.',
    'present_if' => 'El campo :attribute debe estar presente cuando :other es :value.',
    'present_unless' => 'El campo :attribute debe estar presente a menos que :other sea :value.',
    'present_with' => 'El campo :attribute debe estar presente cuando :values esté presente.',
    'present_with_all' => 'El campo :attribute debe estar presente cuando :values estén presentes.',
    'prohibited' => 'El campo :attribute está prohibido.',
'prohibited_if' => 'El campo :attribute está prohibido cuando :other es :value.',
'prohibited_unless' => 'El campo :attribute está prohibido a menos que :other esté en :values.',
'prohibits' => 'El campo :attribute prohíbe que :other esté presente.',
'regex' => 'El formato del campo :attribute es inválido.',
'required' => ':attribute es obligatorio.',
'required_array_keys' => 'El campo :attribute debe contener entradas para: :values.',
'required_if' => 'El campo :attribute es obligatorio cuando :other es :value.',
'required_if_accepted' => 'El campo :attribute es obligatorio cuando :other es aceptado.',
'required_unless' => 'El campo :attribute es obligatorio a menos que :other esté en :values.',
'required_with' => 'El campo :attribute es obligatorio cuando :values está presente.',
'required_with_all' => 'El campo :attribute es obligatorio cuando :values están presentes.',
'required_without' => 'El campo :attribute es obligatorio cuando :values no está presente.',
'required_without_all' => 'El campo :attribute es obligatorio cuando ninguno de :values están presentes.',
'same' => 'El campo :attribute y :other deben coincidir.',
'size' => [
    'array' => 'El campo :attribute debe contener :size elementos.',
    'file' => 'El campo :attribute debe tener :size kilobytes.',
    'numeric' => 'El campo :attribute debe ser :size.',
    'string' => 'El campo :attribute debe tener :size caracteres.',
],
'starts_with' => 'El campo :attribute debe comenzar con uno de los siguientes: :values.',
'string' => 'El campo :attribute debe ser una cadena de texto.',
'timezone' => 'El campo :attribute debe ser una zona horaria válida.',
'unique' => 'El campo :attribute ya ha sido tomado.',
'uploaded' => 'El campo :attribute falló al subir.',
'uppercase' => 'El campo :attribute debe estar en mayúsculas.',
'url' => 'El campo :attribute debe ser una URL válida.',
'ulid' => 'El campo :attribute debe ser un ULID válido.',
'uuid' => 'El campo :attribute debe ser un UUID válido.',

/*
|--------------------------------------------------------------------------
| Líneas de Lenguaje de Validación Personalizadas
|--------------------------------------------------------------------------
|
| Aquí puede especificar mensajes de validación personalizados para atributos 
| usando la convención "attribute.rule" para nombrar las líneas. Esto facilita 
| especificar un mensaje de idioma personalizado para una regla de atributo en particular.
|
*/

'custom' => [
    'nombre-del-atributo' => [
        'nombre-de-la-regla' => 'mensaje-personalizado',
    ],
],

/*
|--------------------------------------------------------------------------
| Atributos de Validación Personalizados
|--------------------------------------------------------------------------
|
| Las siguientes líneas de idioma se usan para intercambiar nuestro marcador de posición
| de atributo con algo más legible para el usuario, como "Dirección de correo electrónico"
| en lugar de "email". Esto simplemente nos ayuda a hacer nuestro mensaje más expresivo.
|
*/

'attributes' => [
    'username' => 'Nombre de usuario',
    'password' => 'Contraseña',
    'dispatches.*.references.*.dispatched_quantity' => 'Cantidad Enviada',
    'dispatches.*.references.*.reference' => 'Referencia / Insumo',
    'references.*.quantity' => 'Cantidad',
    'references.*.reference' => 'Referencia',
    'references.*.batch' => 'N° Lote',
    'reference' => 'Referencia',
    'escencia' => 'Escencia',
    'dipropileno' => 'Dipropileno',
    'disolvente' => 'Disolvente',
    'supplier' => 'Proveedor',
    'supplier_order' => 'Orden de compra - Proveedor',
],

];