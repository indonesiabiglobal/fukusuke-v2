@extends('layouts.master')
@section('title')
    {{ $title ?? 'Documentation' }}
@endsection
@section('css')
    <style>
        .markdown-body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Helvetica, Arial, sans-serif;
            font-size: 16px;
            line-height: 1.6;
            word-wrap: break-word;
        }

        .markdown-body h1,
        .markdown-body h2 {
            border-bottom: 1px solid #eaecef;
            padding-bottom: 0.3em;
        }

        .markdown-body h1 {
            font-size: 2em;
            margin-top: 24px;
            margin-bottom: 16px;
        }

        .markdown-body h2 {
            font-size: 1.5em;
            margin-top: 24px;
            margin-bottom: 16px;
        }

        .markdown-body h3 {
            font-size: 1.25em;
            margin-top: 24px;
            margin-bottom: 16px;
        }

        .markdown-body code {
            background-color: rgba(27, 31, 35, 0.05);
            border-radius: 3px;
            padding: 0.2em 0.4em;
            font-family: "SFMono-Regular", Consolas, "Liberation Mono", Menlo, monospace;
        }

        .markdown-body pre {
            background-color: #f6f8fa;
            border-radius: 6px;
            padding: 16px;
            overflow: auto;
        }

        .markdown-body pre code {
            background-color: transparent;
            padding: 0;
        }

        .markdown-body ul,
        .markdown-body ol {
            padding-left: 2em;
        }

        .markdown-body table {
            border-collapse: collapse;
            width: 100%;
        }

        .markdown-body table th,
        .markdown-body table td {
            border: 1px solid #dfe2e5;
            padding: 6px 13px;
        }

        .markdown-body table th {
            background-color: #f6f8fa;
            font-weight: 600;
        }

        .markdown-body blockquote {
            border-left: 4px solid #dfe2e5;
            color: #6a737d;
            padding-left: 16px;
            margin-left: 0;
        }
    </style>
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <h4 class="card-title mb-0">
                            <i class="mdi mdi-file-document-outline me-2"></i>
                            {{ $title ?? 'Documentation' }}
                        </h4>
                        <a href="{{ url()->previous() }}" class="btn btn-sm btn-secondary">
                            <i class="mdi mdi-arrow-left me-1"></i>
                            Kembali
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="markdown-body">
                        {!! $content !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const markdownBody = document.querySelector('.markdown-body');
            if (markdownBody && markdownBody.textContent.trim().startsWith('#')) {
                // If content looks like markdown, parse it
                const html = marked.parse(markdownBody.textContent);
                markdownBody.innerHTML = html;
            }
        });
    </script>
@endsection
