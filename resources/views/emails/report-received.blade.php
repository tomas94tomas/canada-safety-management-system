<html lang="en">
<head>
    <title>Safety Report Received</title>

    <style>
        .flex {
            display: flex;
        }

        .flex-col {
            flex-direction: column;
        }

        .gap-2 {
            gap: 0.5rem/* 8px */;
        }

        .border {
            border-width: 1px;
        }

        .rounded-md {
            border-radius: 0.375rem/* 6px */;
        }

        .shadow-md {
            --tw-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --tw-shadow-colored: 0 4px 6px -1px var(--tw-shadow-color), 0 2px 4px -2px var(--tw-shadow-color);
            box-shadow: var(--tw-ring-offset-shadow, 0 0 #0000), var(--tw-ring-shadow, 0 0 #0000), var(--tw-shadow);
        }

        .p-3 {
            padding: 0.75rem/* 12px */;
        }

        .m-3 {
            margin: 0.75rem/* 12px */;
        }

        .w-full {
            width: 100%;
        }

        .font-thin {
            font-weight: 100;
        }

        .text-2xl {
            font-size: 1.5rem/* 24px */;
            line-height: 2rem/* 32px */;
        }

        .list-disc {
            list-style-type: disc;
        }

        .m-0 {
            margin: 0px;
        }

        .ml-8 {
            margin-left: 2rem/* 32px */;
        }

        .my-3 {
            margin-top: 0.75rem/* 12px */;
            margin-bottom: 0.75rem/* 12px */;
        }

        .border-b-2 {
            border-bottom-width: 2px;
        }

        .bi2-link {
            display: inline-block;
            margin-top: 1rem;
            padding: 0.5rem 1rem;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 0.375rem;
            font-weight: bold;
            text-align: center;
        }

        .bi2-link:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
<div>
    <div class="flex flex-col border rounded-md shadow-md p-3 m-3">
        <div class="flex flex-col w-full border-b-2">
            <h1 class="text-2xl m-0">Report: {{ $report->subject }}</h1>
            <p class="font-thin m-0">{{ $report->anonymous ? 'Anonymous' : 'Non-Anonymous' }}</p>
        </div>

        <div class="flex flex-col w-full flex-wrap gap-2 my-3">
            @if(!$report->anonymous)
                <p class="font-thin w-full">Email: <strong>{{ $report->email }}</strong></p>
            @endif

            <p class="font-thin w-full">Location: <strong>{{ $report->location }}</strong></p>
            <p class="font-thin w-full">Description: <strong>{{ $report->description }}</strong></p>
            <p class="font-thin w-full">Proposal: <strong>{{ $report->proposal }}</strong></p>
                <!-- BI2 Link -->
                <div class="flex justify-center">
                    <a href="https://bi2-v2.fltechnics.com/safety_management_system_report?page=1&search={{ urlencode($report->sms_number) }}" class="bi2-link" target="_blank">
                        Open BI2 to see the new report
                    </a>
                </div>
{{--            <div class="flex flex-col">--}}
{{--                <p class="font-thin">Attachments: </p>--}}

{{--                <ul class="list-disc ml-8">--}}
{{--                    @foreach($report->attachments as $attachment)--}}
{{--                        <li><a href="{{ asset($attachment->file_path) }}" target="_blank" download>{{ $attachment->file_original_name }}</a></li>--}}
{{--                    @endforeach--}}
{{--                </ul>--}}
{{--            </div>--}}
        </div>
    </div>
</div>
</body>
</html>
