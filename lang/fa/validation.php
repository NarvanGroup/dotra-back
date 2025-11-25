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

    'accepted' => ':attribute باید تایید شود.',
    'accepted_if' => 'وقتی :other برابر :value است، :attribute باید تایید شود.',
    'active_url' => ':attribute باید یک آدرس URL معتبر باشد.',
    'after' => ':attribute باید تاریخی بعد از :date باشد.',
    'after_or_equal' => ':attribute باید تاریخی بعد یا مساوی :date باشد.',
    'alpha' => ':attribute فقط می‌تواند شامل حروف باشد.',
    'alpha_dash' => ':attribute فقط می‌تواند شامل حروف، اعداد، خط تیره و زیرخط باشد.',
    'alpha_num' => ':attribute فقط می‌تواند شامل حروف و اعداد باشد.',
    'any_of' => ':attribute معتبر نیست.',
    'array' => ':attribute باید آرایه باشد.',
    'ascii' => ':attribute باید فقط شامل نویسه‌های تک‌بایتی و عددی-حرفی باشد.',
    'before' => ':attribute باید تاریخی قبل از :date باشد.',
    'before_or_equal' => ':attribute باید تاریخی قبل یا مساوی :date باشد.',
    'between' => [
        'array' => ':attribute باید بین :min تا :max آیتم داشته باشد.',
        'file' => 'حجم :attribute باید بین :min تا :max کیلوبایت باشد.',
        'numeric' => ':attribute باید بین :min تا :max باشد.',
        'string' => ':attribute باید بین :min تا :max کاراکتر باشد.',
    ],
    'boolean' => ':attribute باید مقدار بولی (درست یا نادرست) داشته باشد.',
    'can' => ':attribute شامل مقدار مجاز نیست.',
    'confirmed' => 'تاییدیه :attribute با آن یکسان نیست.',
    'contains' => ':attribute یک مقدار ضروری کم دارد.',
    'current_password' => 'رمز عبور فعلی نادرست است.',
    'date' => ':attribute باید یک تاریخ معتبر باشد.',
    'date_equals' => ':attribute باید تاریخی مساوی :date باشد.',
    'date_format' => ':attribute باید با الگوی :format مطابقت داشته باشد.',
    'decimal' => ':attribute باید :decimal رقم اعشار داشته باشد.',
    'declined' => ':attribute باید رد شود.',
    'declined_if' => 'وقتی :other برابر :value است، :attribute باید رد شود.',
    'different' => ':attribute و :other باید متفاوت باشند.',
    'digits' => ':attribute باید :digits رقم باشد.',
    'digits_between' => ':attribute باید بین :min تا :max رقم باشد.',
    'dimensions' => 'ابعاد تصویر :attribute نامعتبر است.',
    'distinct' => ':attribute مقدار تکراری دارد.',
    'doesnt_contain' => ':attribute نباید هیچ‌یک از موارد زیر را شامل شود: :values.',
    'doesnt_end_with' => ':attribute نباید با یکی از مقادیر زیر تمام شود: :values.',
    'doesnt_start_with' => ':attribute نباید با یکی از مقادیر زیر شروع شود: :values.',
    'email' => ':attribute باید یک ایمیل معتبر باشد.',
    'ends_with' => ':attribute باید با یکی از مقادیر زیر تمام شود: :values.',
    'enum' => ':attribute انتخاب‌شده معتبر نیست.',
    'exists' => ':attribute انتخاب‌شده معتبر نیست.',
    'extensions' => ':attribute باید یکی از پسوندهای زیر را داشته باشد: :values.',
    'file' => ':attribute باید یک فایل باشد.',
    'filled' => ':attribute باید مقدار داشته باشد.',
    'gt' => [
        'array' => ':attribute باید بیش از :value آیتم داشته باشد.',
        'file' => 'حجم :attribute باید بیشتر از :value کیلوبایت باشد.',
        'numeric' => ':attribute باید بزرگتر از :value باشد.',
        'string' => ':attribute باید بیش از :value کاراکتر باشد.',
    ],
    'gte' => [
        'array' => ':attribute باید حداقل :value آیتم داشته باشد.',
        'file' => 'حجم :attribute باید بیشتر یا مساوی :value کیلوبایت باشد.',
        'numeric' => ':attribute باید بیشتر یا مساوی :value باشد.',
        'string' => ':attribute باید بیشتر یا مساوی :value کاراکتر باشد.',
    ],
    'hex_color' => ':attribute باید یک رنگ هگزادسیمال معتبر باشد.',
    'image' => ':attribute باید یک تصویر باشد.',
    'in' => ':attribute انتخاب‌شده معتبر نیست.',
    'in_array' => ':attribute باید در :other موجود باشد.',
    'in_array_keys' => ':attribute باید حداقل یکی از کلیدهای زیر را داشته باشد: :values.',
    'integer' => ':attribute باید عدد صحیح باشد.',
    'ip' => ':attribute باید یک آدرس IP معتبر باشد.',
    'ipv4' => ':attribute باید یک آدرس IPv4 معتبر باشد.',
    'ipv6' => ':attribute باید یک آدرس IPv6 معتبر باشد.',
    'json' => ':attribute باید یک رشته JSON معتبر باشد.',
    'list' => ':attribute باید یک لیست باشد.',
    'lowercase' => ':attribute باید با حروف کوچک نوشته شود.',
    'lt' => [
        'array' => ':attribute باید کمتر از :value آیتم داشته باشد.',
        'file' => 'حجم :attribute باید کمتر از :value کیلوبایت باشد.',
        'numeric' => ':attribute باید کمتر از :value باشد.',
        'string' => ':attribute باید کمتر از :value کاراکتر باشد.',
    ],
    'lte' => [
        'array' => ':attribute نباید بیش از :value آیتم داشته باشد.',
        'file' => 'حجم :attribute باید کمتر یا مساوی :value کیلوبایت باشد.',
        'numeric' => ':attribute باید کمتر یا مساوی :value باشد.',
        'string' => ':attribute باید کمتر یا مساوی :value کاراکتر باشد.',
    ],
    'mac_address' => ':attribute باید یک آدرس MAC معتبر باشد.',
    'max' => [
        'array' => ':attribute نباید بیش از :max آیتم داشته باشد.',
        'file' => 'حجم :attribute نباید بیشتر از :max کیلوبایت باشد.',
        'numeric' => ':attribute نباید بزرگتر از :max باشد.',
        'string' => ':attribute نباید بیش از :max کاراکتر باشد.',
    ],
    'max_digits' => ':attribute نباید بیش از :max رقم داشته باشد.',
    'mimes' => ':attribute باید فایلی از نوع: :values باشد.',
    'mimetypes' => ':attribute باید فایلی از نوع: :values باشد.',
    'min' => [
        'array' => ':attribute باید حداقل :min آیتم داشته باشد.',
        'file' => 'حجم :attribute باید حداقل :min کیلوبایت باشد.',
        'numeric' => ':attribute باید حداقل :min باشد.',
        'string' => ':attribute باید حداقل :min کاراکتر باشد.',
    ],
    'min_digits' => ':attribute باید حداقل :min رقم داشته باشد.',
    'missing' => ':attribute باید موجود نباشد.',
    'missing_if' => 'وقتی :other برابر :value است، :attribute باید موجود نباشد.',
    'missing_unless' => ':attribute باید نباشد مگر اینکه :other برابر :value باشد.',
    'missing_with' => 'وقتی :values موجود است، :attribute باید موجود نباشد.',
    'missing_with_all' => 'وقتی :values موجود هستند، :attribute باید موجود نباشد.',
    'multiple_of' => ':attribute باید مضربی از :value باشد.',
    'not_in' => ':attribute انتخاب‌شده معتبر نیست.',
    'not_regex' => 'فرمت :attribute نامعتبر است.',
    'numeric' => ':attribute باید عددی باشد.',
    'password' => [
        'letters' => ':attribute باید حداقل یک حرف داشته باشد.',
        'mixed' => ':attribute باید حداقل یک حرف بزرگ و یک حرف کوچک داشته باشد.',
        'numbers' => ':attribute باید حداقل یک عدد داشته باشد.',
        'symbols' => ':attribute باید حداقل یک نماد داشته باشد.',
        'uncompromised' => ':attribute در نشت اطلاعاتی دیده شده است. لطفاً :attribute دیگری انتخاب کنید.',
    ],
    'present' => ':attribute باید موجود باشد.',
    'present_if' => 'وقتی :other برابر :value است، :attribute باید موجود باشد.',
    'present_unless' => ':attribute باید موجود باشد مگر اینکه :other برابر :value باشد.',
    'present_with' => 'وقتی :values موجود است، :attribute باید موجود باشد.',
    'present_with_all' => 'وقتی :values موجود هستند، :attribute باید موجود باشد.',
    'prohibited' => ':attribute مجاز نیست.',
    'prohibited_if' => 'وقتی :other برابر :value است، :attribute مجاز نیست.',
    'prohibited_if_accepted' => 'وقتی :other پذیرفته شده است، :attribute مجاز نیست.',
    'prohibited_if_declined' => 'وقتی :other رد شده است، :attribute مجاز نیست.',
    'prohibited_unless' => ':attribute مجاز نیست مگر اینکه :other در :values باشد.',
    'prohibits' => ':attribute مانع از حضور :other می‌شود.',
    'regex' => 'فرمت :attribute نامعتبر است.',
    'required' => 'پر کردن :attribute الزامی است.',
    'required_array_keys' => ':attribute باید شامل کلیدهای زیر باشد: :values.',
    'required_if' => 'وقتی :other برابر :value است، پر کردن :attribute الزامی است.',
    'required_if_accepted' => 'وقتی :other پذیرفته شده است، پر کردن :attribute الزامی است.',
    'required_if_declined' => 'وقتی :other رد شده است، پر کردن :attribute الزامی است.',
    'required_unless' => 'پر کردن :attribute الزامی است مگر اینکه :other در :values باشد.',
    'required_with' => 'وقتی :values موجود است، پر کردن :attribute الزامی است.',
    'required_with_all' => 'وقتی :values موجود هستند، پر کردن :attribute الزامی است.',
    'required_without' => 'وقتی :values موجود نیست، پر کردن :attribute الزامی است.',
    'required_without_all' => 'وقتی هیچ‌یک از :values موجود نیست، پر کردن :attribute الزامی است.',
    'same' => ':attribute باید با :other مطابقت داشته باشد.',
    'size' => [
        'array' => ':attribute باید دقیقاً :size آیتم داشته باشد.',
        'file' => 'حجم :attribute باید :size کیلوبایت باشد.',
        'numeric' => ':attribute باید برابر :size باشد.',
        'string' => ':attribute باید :size کاراکتر باشد.',
    ],
    'starts_with' => ':attribute باید با یکی از این مقادیر شروع شود: :values.',
    'string' => ':attribute باید یک رشته باشد.',
    'timezone' => ':attribute باید یک منطقه زمانی معتبر باشد.',
    'unique' => ':attribute قبلاً استفاده شده است.',
    'uploaded' => 'بارگذاری :attribute انجام نشد.',
    'uppercase' => ':attribute باید با حروف بزرگ نوشته شود.',
    'url' => ':attribute باید یک آدرس URL معتبر باشد.',
    'ulid' => ':attribute باید یک ULID معتبر باشد.',
    'uuid' => ':attribute باید یک UUID معتبر باشد.',

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

    'attributes' => [],

];
