<?php

declare(strict_types=1);
ob_start();
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark bg-opacity-50 border-bottom border-light border-opacity-25">
    <div class="container-fluid px-4">
        <span class="navbar-brand fw-bold"><i class="bi bi-grid-1x2-fill me-2"></i>Repositorio Proyectos PASS</span>
        <div class="d-flex align-items-center gap-3 text-white">
            <span class="brand-pill"><i class="bi bi-person-circle me-1"></i><?php echo htmlspecialchars((string) ($user['username'] ?? 'N/A'), ENT_QUOTES, 'UTF-8'); ?></span>
            <a href="<?php echo htmlspecialchars($basePath . '/logout', ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-outline-light btn-sm"><i class="bi bi-box-arrow-right me-1"></i>Salir</a>
        </div>
    </div>
</nav>

<div class="container py-4">
    <div class="card table-card shadow-lg border-0">
        <div class="card-body p-4">
            <h2 class="h4 fw-bold mb-3">Proyectos</h2>
            <div class="table-responsive">
                <table id="projectsTable" class="table table-striped table-hover align-middle w-100">
                    <thead>
                    <tr>
                        <th>Nombre del proyecto</th>
                        <th>URL</th>
                        <th>Documentacion</th>
                    </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade modal-doc" id="docModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-file-earmark-richtext me-2"></i>Documentacion del proyecto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body doc-content" id="docModalContent">
                <div class="text-center py-5 text-muted">Selecciona un proyecto para cargar la documentacion.</div>
            </div>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
ob_start();
?>
<script>
$(function () {
    var basePath = <?php echo json_encode($basePath, JSON_UNESCAPED_SLASHES); ?>;

    var table = $('#projectsTable').DataTable({
        ajax: basePath + '/api/projects',
        columns: [
            { data: 'name' },
            {
                data: 'project_url',
                render: function (data) {
                    return '<a href="' + data + '" target="_blank" rel="noopener" class="link-primary">' + data + '</a>';
                }
            },
            {
                data: 'id',
                orderable: false,
                searchable: false,
                render: function (id) {
                    return '<button class="btn btn-sm btn-primary btn-view-doc" data-id="' + id + '"><i class="bi bi-eye-fill me-1"></i>Ver</button>';
                }
            }
        ],
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/es-ES.json'
        }
    });

    $(document).on('click', '.btn-view-doc', function () {
        var id = $(this).data('id');
        var modalElement = document.getElementById('docModal');
        var modal = bootstrap.Modal.getOrCreateInstance(modalElement);

        $('#docModalContent').html('<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-3 mb-0">Cargando documentacion...</p></div>');
        modal.show();

        $.getJSON(basePath + '/api/projects/' + id + '/doc')
            .done(function (response) {
                if (response.success) {
                    $('#docModalContent').html(response.html);
                } else {
                    $('#docModalContent').html('<div class="alert alert-warning shadow-sm"><i class="bi bi-info-circle-fill me-2"></i>' + response.message + '</div>');
                }
            })
            .fail(function () {
                $('#docModalContent').html('<div class="alert alert-danger shadow-sm"><i class="bi bi-x-octagon-fill me-2"></i>No se pudo cargar la documentacion en este momento.</div>');
            });
    });
});
</script>
<?php
$scripts = ob_get_clean();
include __DIR__ . '/layout.php';
