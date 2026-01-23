<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Blog File Upload Test</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        h1 { color: #667eea; margin-bottom: 30px; }
        .upload-section {
            margin-bottom: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
        }
        .upload-section h3 { margin-bottom: 15px; color: #333; }
        input[type="file"] { margin: 10px 0; }
        button {
            background: #667eea;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            margin-right: 10px;
        }
        button:hover { background: #764ba2; }
        .result {
            margin-top: 20px;
            padding: 15px;
            border-radius: 8px;
            display: none;
        }
        .result.success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; }
        .result.error { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; }
        pre { background: #f4f4f4; padding: 10px; border-radius: 5px; overflow-x: auto; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>📤 Blog File Upload Testing</h1>

        <div class="upload-section">
            <h3>Upload Image</h3>
            <input type="file" id="imageFile" accept="image/*">
            <button onclick="uploadFile('image', 'imageFile', 'imageResult')">Upload Image</button>
            <div id="imageResult" class="result"></div>
        </div>

        <div class="upload-section">
            <h3>Upload Document</h3>
            <input type="file" id="docFile" accept=".pdf,.doc,.docx,.xls,.xlsx">
            <button onclick="uploadFile('document', 'docFile', 'docResult')">Upload Document</button>
            <div id="docResult" class="result"></div>
        </div>

        <div class="upload-section">
            <h3>Upload Video</h3>
            <input type="file" id="videoFile" accept="video/*">
            <button onclick="uploadFile('video', 'videoFile', 'videoResult')">Upload Video</button>
            <div id="videoResult" class="result"></div>
        </div>

        <div class="upload-section">
            <h3>Upload Attachment (Any File)</h3>
            <input type="file" id="attachFile">
            <button onclick="uploadFile('attachment', 'attachFile', 'attachResult')">Upload Attachment</button>
            <div id="attachResult" class="result"></div>
        </div>
    </div>

    <script>
        async function uploadFile(type, inputId, resultId) {
            const fileInput = document.getElementById(inputId);
            const resultDiv = document.getElementById(resultId);
            const file = fileInput.files[0];

            if (!file) {
                showResult(resultDiv, 'error', 'Please select a file first');
                return;
            }

            const formData = new FormData();
            formData.append('file', file);
            formData.append('generate_thumbnail', 'true');

            resultDiv.style.display = 'block';
            resultDiv.className = 'result';
            resultDiv.innerHTML = 'Uploading...';

            try {
                const response = await fetch(`/api/upload/${type}`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const data = await response.json();

                if (data.success) {
                    let html = `<strong>✅ Upload Successful!</strong><br><br>`;
                    html += `<strong>Filename:</strong> ${data.data.filename}<br>`;
                    html += `<strong>Type:</strong> ${data.data.type}<br>`;
                    html += `<strong>Size:</strong> ${(data.data.size / 1024).toFixed(2)} KB<br>`;
                    html += `<strong>URL:</strong> <a href="${data.data.url}" target="_blank">${data.data.url}</a><br>`;
                    
                    if (data.data.thumbnail) {
                        html += `<strong>Thumbnail:</strong> <a href="${data.data.thumbnail}" target="_blank">View</a><br>`;
                    }
                    
                    html += `<pre>${JSON.stringify(data.data, null, 2)}</pre>`;
                    showResult(resultDiv, 'success', html);
                } else {
                    showResult(resultDiv, 'error', `Upload failed: ${data.message}`);
                }
            } catch (error) {
                showResult(resultDiv, 'error', `Error: ${error.message}`);
            }
        }

        function showResult(div, type, message) {
            div.style.display = 'block';
            div.className = `result ${type}`;
            div.innerHTML = message;
        }
    </script>
</body>
</html>
