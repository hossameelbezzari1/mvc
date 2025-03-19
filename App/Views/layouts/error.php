<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? htmlspecialchars($title) : 'Error' ?></title>
    <link href="/assets/css/tailwind.css" rel="stylesheet">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
            background-color: #f7f7f7;
            color: #333;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .error-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 20px;
            width: 90%;
            max-width: 600px;
            text-align: left;
        }

        .error-header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .error-header .circle {
            width: 30px;
            height: 30px;
            background: #e74c3c;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 14px;
            margin-right: 10px;
        }

        .error-title {
            font-size: 20px;
            color: #e74c3c;
            margin: 0;
        }

        .error-message {
            margin-bottom: 20px;
            color: #666;
        }

        .stack-trace {
            background: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 10px;
            font-family: Consolas, Monaco, monospace;
            font-size: 14px;
            color: #333;
            white-space: pre-wrap;
        }

        .share-options {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        .share-options input[type="checkbox"] {
            margin-right: 5px;
        }

        .share-options button {
            background: #8e44ad;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
        }

        .share-options button:hover {
            background: #732d91;
        }
    </style>
</head>

<body>
    <div class="error-container">
        <div class="error-header">
            <div class="circle">!</div>
            <h1 class="error-title"><?= isset($title) ? htmlspecialchars($title) : 'Internal Server Error' ?></h1>
        </div>
        <?php if (isset($message)) : ?>
            <div class="error-message"><?= nl2br(htmlspecialchars($message)) ?></div>
        <?php endif; ?>
        <?php if (isset($content)) : ?>
            <?= $content ?>
        <?php endif; ?>
        <?php if (isset($exception)) : ?>
            <div class="stack-trace">
                <?= nl2br(htmlspecialchars($exception)) ?>
            </div>
        <?php endif; ?>
        <div class="share-options">
            <label><input type="checkbox" name="share_flare"> Share with Flare</label>
            <label><input type="checkbox" name="stack_trace" checked> Stack trace</label>
            <label><input type="checkbox" name="request_context" checked> Request context</label>
            <label><input type="checkbox" name="database_queries" checked> Database queries</label>
            <button>Create share</button>
        </div>
    </div>
</body>

</html>