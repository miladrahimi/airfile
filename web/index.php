<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>AirFile | An online clipboard to share files!</title>
    <meta name="description" content="An online clipboard to share files!">
    <meta name="keywords" content="Clipboard, Online, File, Share">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="index,follow">
    <link rel="icon" href="favicon.png">
    <link rel="apple-touch-icon" href="favicon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style type="text/css">
        textarea {
            height: 40vh;
        }
    </style>
</head>
<body>

<main class="container mt-5 text-center">
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <h1 class="display-5">AirFile</h1>
            <p class="lead">An online clipboard to share files!</p>

            <ul class="nav nav-tabs mt-5" id="tabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="save-tab" data-bs-toggle="tab" data-bs-target="#save"
                            type="button" role="tab" aria-controls="save" aria-selected="true">Save
                    </button>
                </li>
                <li class="nav-item flex-fill" role="presentation">
                    <button class="nav-link" id="load-tab" data-bs-toggle="tab" data-bs-target="#load"
                            type="button" role="tab" aria-controls="load" aria-selected="false">Load
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="tabsContent">
                <div class="tab-pane fade show active" id="save" role="tabpanel" aria-labelledby="save-tab">
                    <div class="card bg-light text-left border-top-0 rounded-0 rounded-bottom">
                        <div class="card-body d-flex flex-column gap-2">
                            <div class="alert alert-dark m-0 text-start" id="save-alert">
                                Choose the file to save...
                            </div>
                            <input type="file" class="form-control" id="save-file">
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="load" role="tabpanel" aria-labelledby="load-tab">
                    <div class="card bg-light text-left border-top-0 rounded-0 rounded-bottom">
                        <div class="card-body d-flex flex-column gap-2">
                            <input type="text" id="load-name" placeholder="File Name" title="File Name"
                                   class="form-control">
                            <div class="alert alert-dark m-0 text-start" id="load-alert">
                                Enter file name to get the download URL.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <p class="mt-5 text-muted small">
                &copy; <?php echo date('Y') ?> by
                <a href="https://miladrahimi.com" title="Milad Rahimi">Milad Rahimi</a> |
                <a href="https://github.com/miladrahimi/airfile" title="GitHub Repository">GitHub</a>
            </p>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"></script>
<script>
    $(document).ready(function () {
        // Tabs
        let triggerTabList = [].slice.call(document.querySelectorAll('#tabs a'))
        triggerTabList.forEach(function (triggerEl) {
            let tabTrigger = new bootstrap.Tab(triggerEl)
            triggerEl.addEventListener('click', function (event) {
                event.preventDefault()
                tabTrigger.show()
            })
        })

        // Save (Button)
        $('#save-file').change(function () {
            let me = $(this)
            let alert = $('#save-alert')

            me.prop('disabled', true)
            alert.html('Saving...')


            const formData = new FormData()
            formData.append('file', $(this)[0].files[0])

            let request = $.ajax({
                url: 'process.php',
                type: 'post',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
            })

            request.done(function (response) {
                alert.html([
                    'File Name: ' + response['name'],
                    'File URL: ' + `<a href="${response['url']}">${response['url']}<a>`,
                ].join('<br>'))
                me.val('').prop('disabled', false)
            })

            request.fail(function (jqXHR, textStatus, errorThrown) {
                console.error(jqXHR, textStatus, errorThrown)
                alert.html('Failed to save :(')
                me.prop('disabled', false)
            })
        })

        // Load
        $('#load-name').bind('input', function () {
            let name = $(this).val()
            let alert = $('#load-alert')
            alert.html('Loading...')

            let request = $.ajax({url: 'process.php', type: 'get', data: {'name': name}})

            request.done(function (response) {
                alert.html(`<a href="${response['url']}">${response['url']}<a>`)
            })

            request.fail(function (jqXHR, textStatus, errorThrown) {
                if (jqXHR.status === 404) {
                    alert.html('ERROR: File not found :(')
                } else {
                    console.error(jqXHR, textStatus, errorThrown)
                    alert.html('ERROR: Failed to load the file URL :(')
                }
            })
        })
    })
</script>

</body>
</html>
