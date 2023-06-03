<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => 'El campo :attribute debe ser aceptado.',
    'accepted_if' => 'El campo :attribute debe ser aceptado cuando :other es :value.',
    'active_url' => 'El campo :attribute no es una URL válida.',
    'after' => 'El campo :attribute debe ser una fecha posterior a :date.',
    'after_or_equal' => 'El campo :attribute debe ser una fecha posterior o igual a :date.',
    'alpha' => 'El campo :attribute solamente puede contener letras.',
    'alpha_dash' => 'El campo :attribute solamente puede contener letras, números, guiones y guiones bajos.',
    'alpha_num' => 'El campo :attribute solamente puede contener letras y números.',
    'array' => 'El campo :attribute debe ser un arreglo.',
    'before' => 'El campo :attribute debe ser una fecha anterior a :date.',
    'before_or_equal' => 'El campo :attribute debe ser una fecha anterior o igual a :date.',
    'between' => [
        'array' => 'El campo :attribute debe tener entre :min y :max elementos.',
        'file' => 'El campo :attribute debe estar entre :min y :max kilobytes.',
        'numeric' => 'El campo :attribute debe estar entre :min y :max.',
        'string' => 'El campo :attribute debe tener entre :min y :max caracteres.',
    ],
    'boolean' => 'El campo :attribute debe ser falso o verdadero.',
    'confirmed' => 'El campo de confirmación :attribute no coincide.',
    'current_password' => 'La contraseña es incorrecta.',
    'date' => 'El campo :attribute no es una fecha válida.',
    'date_equals' => 'El campo :attribute debe ser una fecha igual a :date.',
    'date_format' => 'El campo :attribute no coincide con el formato :format.',
    'declined' => 'El campo :attribute debe ser declinado.',
    'declined_if' => 'El campo :attribute debe ser declinado cuando :other es :value.',
    'different' => 'El campo :attribute y :other deben ser diferentes.',
    'digits' => 'El campo :attribute debe tener :digits dígitos.',
    'digits_between' => 'El campo :attribute debe estar entre :min y :max dígitos.',
    'dimensions' => 'El campo :attribute tiene dimensiones de imagen inválidas.',
    'distinct' => 'El campo :attribute tiene un valor duplicado.',
    'email' => 'El campo :attribute debe ser un correo electrónico válido.',
    'ends_with' => 'El campo :attribute debe terminar con alguno de los siguientes valores: :values.',
    'enum' => 'El campo seleccionado :attribute no es válido.',
    'exists' => 'El campo seleccionado :attribute no es válido.',
    'file' => 'El campo :attribute debe ser un archivo.',
    'filled' => 'El campo :attribute debe tener un valor.',
    'gt' => [
        'array' => 'El campo :attribute must have more than :value items.',
        'file' => 'El campo :attribute debe ser mayor que :value kilobytes.',
        'numeric' => 'El campo :attribute debe ser mayor que :value.',
        'string' => 'El campo :attribute debe ser mayor que :value caracteres.',
    ],
    'gte' => [
        'array' => 'El campo :attribute must have :value items or more.',
        'file' => 'El campo :attribute debe ser mayor que or equal :value kilobytes.',
        'numeric' => 'El campo :attribute debe ser mayor que or equal :value.',
        'string' => 'El campo :attribute debe ser mayor que or equal :value caracteres.',
    ],
    'image' => 'El campo :attribute debe ser una imagen.',
    'in' => 'El campo seleccionado :attribute no es válido.',
    'in_array' => 'El campo :attribute no existe en :other.',
    'integer' => 'El campo :attribute debe ser un número entero.',
    'ip' => 'El campo :attribute debe ser una dirección IP válida.',
    'ipv4' => 'El campo :attribute debe ser una dirección IPv4 válida.',
    'ipv6' => 'El campo :attribute debe ser una dirección IPv6 válida.',
    'json' => 'El campo :attribute debe ser un JSON string válido.',
    'lt' => [
        'array' => 'The :attribute must have less than :value items.',
        'file' => 'The :attribute must be less than :value kilobytes.',
        'numeric' => 'The :attribute must be less than :value.',
        'string' => 'The :attribute must be less than :value characters.',
    ],
    'lte' => [
        'array' => 'The :attribute must not have more than :value items.',
        'file' => 'The :attribute must be less than or equal to :value kilobytes.',
        'numeric' => 'The :attribute must be less than or equal to :value.',
        'string' => 'The :attribute must be less than or equal to :value characters.',
    ],
    'mac_address' => 'The :attribute must be a valid MAC address.',
    'max' => [
        'array' => 'The :attribute must not have more than :max items.',
        'file' => 'The :attribute must not be greater than :max kilobytes.',
        'numeric' => 'The :attribute must not be greater than :max.',
        'string' => 'The :attribute must not be greater than :max characters.',
    ],
    'mimes' => 'The :attribute must be a file of type: :values.',
    'mimetypes' => 'The :attribute must be a file of type: :values.',
    'min' => [
        'array' => 'The :attribute must have at least :min items.',
        'file' => 'The :attribute must be at least :min kilobytes.',
        'numeric' => 'The :attribute must be at least :min.',
        'string' => 'El campo :attribute debe tener al menos :min caracteres.',
    ],
    'multiple_of' => 'The :attribute must be a multiple of :value.',
    'not_in' => 'The selected :attribute is invalid.',
    'not_regex' => 'The :attribute format is invalid.',
    'numeric' => 'The :attribute must be a number.',
    'password' => [
        'letters' => 'El campo :attribute debe contener al menos una letra.',
        'mixed' => 'El campo :attribute debe contener al menos una mayúscula y una minúscula.',
        'numbers' => 'El campo :attribute debe contener al menos un número.',
        'symbols' => 'El campo :attribute debe contener al menos un símbolo.',
        'uncompromised' => 'El campo dado :attribute ha aparecido en una fuga de datos. Por favor, selecciona un diferente :attribute.',
    ],
    'present' => 'The :attribute field must be present.',
    'prohibited' => 'The :attribute field is prohibited.',
    'prohibited_if' => 'The :attribute field is prohibited when :other is :value.',
    'prohibited_unless' => 'The :attribute field is prohibited unless :other is in :values.',
    'prohibits' => 'The :attribute field prohibits :other from being present.',
    'regex' => 'The :attribute format is invalid.',
    'required' => 'El campo :attribute es requerido.',
    'required_array_keys' => 'The :attribute field must contain entries for: :values.',
    'required_if' => 'The :attribute field is required when :other is :value.',
    'required_unless' => 'The :attribute field is required unless :other is in :values.',
    'required_with' => 'The :attribute field is required when :values is present.',
    'required_with_all' => 'The :attribute field is required when :values are present.',
    'required_without' => 'The :attribute field is required when :values is not present.',
    'required_without_all' => 'The :attribute field is required when none of :values are present.',
    'same' => 'The :attribute and :other must match.',
    'size' => [
        'array' => 'The :attribute must contain :size items.',
        'file' => 'The :attribute must be :size kilobytes.',
        'numeric' => 'The :attribute must be :size.',
        'string' => 'The :attribute must be :size characters.',
    ],
    'starts_with' => 'El campo :attribute debe comenzar con uno de los siguientes valores: :values',
    'string' => 'El campo :attribute debe ser una cadena.',
    'timezone' => 'El campo :attribute debe ser una zona horaria válida.',
    'unique' => 'El campo :attribute ya existe.',
    'uploaded' => 'Error al subir :attribute.',
    'url' => 'El formato de :attribute es inválido.',
    'uuid' => 'El campo :attribute debe ser un UUID válido.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [
        // generales
        'name' => 'nombre',
        'email' => 'correo electrónico',
        //direcciones
        'departamento_id' => 'departamento',
        'municipio_id' => 'municipio',
        'identificador_casa' => 'identificador de casa',
        'apto_local' => 'número de apartamento o local',
        'codigo_iso' => 'código',

        //ingresos
        'forma_aplicacion' => 'forma de aplicación',

        //Empresas
        'nrc' => 'el número de NRC',
        'nit' => 'NIT',
        //Puestos
        'nro_plazas' => 'número de plazas',
        'salario_desde' => 'salario mínimo',
        'salario_hasta' => 'salario máximo',
        // Centro de costos
        'anyo' => 'año',
        // Planillas
        'anyo_periodo' => 'año período',
        'fecha_generacion' => 'fecha de generación',
        // Empleados
        'primer_nombre' => 'primer nombre',
        'segundo_nombre' => 'segundo nombre',
        'apellido_paterno' => 'apellido paterno',
        'apellido_materno' => 'apellido manternos',
        'tipo_documentos_id' => 'tipo de documento',
        'identificacion' => 'identifación',
        'fecha_nacimiento' => 'fecha de nacimiento',
        'estados_civiles_id' => 'estado civil',
        'email_personal' => 'correo personal',
        'direcciones_id' => 'dirección',
        'email_profesional' => 'correo profesional',
        'ocupaciones_id' => 'ocupación',
        'puestos_id' => 'puesto',
        'isss' => 'número del ISS',
        'generos_id' => 'género',
        'fecha_ingreso' => 'fecha de ingreso',
        'nup' => 'NUP',
        // Tabla renta mensual
        'porcentaje_aplicar' => 'porcentaje a aplicar',
        'mas_fija' => 'más cuto fija',
        // Techo Laboral
        'afp' => 'AFP'
    ],

];
