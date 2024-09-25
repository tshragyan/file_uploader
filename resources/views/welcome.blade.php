<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Uploader</title>
</head>
<body>
<input type="file" id="file">
<progress id="progress" value="0" max="100"></progress>
<h1 id="message"></h1>
@csrf

<script>
    const input = document.getElementById('file');
    const progress = document.getElementById('progress');
    const message = document.getElementById('message');
    message.innerText = ''
    const chunkSize = 5 * 1024 * 1024;
    let uploadProgress = 0;

    input.addEventListener('change', async () => {
        const file = input.files[0];
        const totalCount = Math.ceil(file.size / chunkSize);
        const token = document.querySelector('input[name="_token"]').value;

        for (let index = 0; index < totalCount; index++) {
            const start = index * chunkSize;
            const end = Math.min(file.size, start + chunkSize);
            const chunk = file.slice(start, end);

            const formData = new FormData();
            formData.append('file', chunk);
            formData.append('_token', token);
            formData.append('fileName', file.name);
            formData.append('index', index);
            formData.append('totalCount', totalCount);

            try {
                const response = await fetch('/upload', {
                    method: 'POST',
                    body: formData,
                });

                if (response.ok) {
                    uploadProgress += (chunk.size / file.size) * 100;
                    progress.value = uploadProgress;
                    if (uploadProgress === 100) {
                        message.innerText = 'Uploaded!'
                    }
                } else {
                    message.innerText = 'Something went wrong!'
                }
            } catch (error) {
                console.error('Upload failed', error);
            }
        }
    });
</script>
</body>
</html>
