<!DOCTYPE html>
<html lang="">
@component('mail::message')
    # Заявка завершена

    Здравствуйте, {{ $request->name }}!

    Ваша заявка была завершена ответственным лицом.

    Ниже приведен комментарий, оставленный ответственным лицом:

    {{ $request->comment }}

    Спасибо за обращение!

    С уважением, {{ config('app.name') }}
@endcomponent
</html>
