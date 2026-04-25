@component('mail::message')
# Hello, {{ $assigneeName }}!

You have been assigned a new task in **TaskFlow**.

@component('mail::panel')
**Task:** {{ $taskTitle }}
**Priority:** {{ strtoupper($taskPriority) }}
**Status:** {{ str_replace('_', ' ', ucfirst($taskStatus)) }}
**Project:** {{ $projectName }}
@endcomponent

Please log in to view the full details and get started.

@component('mail::button', ['url' => config('app.url')])
View Task
@endcomponent

Thanks,
{{ config('app.name') }}
@endcomponent