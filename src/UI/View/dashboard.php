<?php

declare(strict_types=1);
ob_start();
?>
<nav class="navbar topbar-premium">
    <div class="container-fluid px-4 py-2">
        <div class="d-flex align-items-center gap-3">
            <span class="brand-mark"><i class="bi bi-grid-1x2-fill"></i></span>
            <div>
                <div class="fw-bold">Repositorio Proyectos PASS</div>
                <small class="text-light opacity-75">Panel corporativo de documentación</small>
            </div>
        </div>
        <div class="d-flex align-items-center gap-3">
            <span class="user-chip"><i class="bi bi-person-circle me-1"></i><?php echo htmlspecialchars((string) ($user['username'] ?? 'N/A'), ENT_QUOTES, 'UTF-8'); ?></span>
            <a href="<?php echo htmlspecialchars($basePath . '/logout', ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-outline-light btn-sm"><i class="bi bi-box-arrow-right me-1"></i>Salir</a>
        </div>
    </div>
</nav>

<div class="container premium-shell">
    <div class="premium-glass premium-card">
        <h2 class="h4 fw-bold mb-3">Proyectos</h2>
        <div class="table-responsive">
            <table id="projectsTable" class="table table-striped table-hover align-middle w-100 mb-0">
                <thead>
                <tr>
                    <th>Nombre del proyecto</th>
                    <th>URL</th>
                    <th>Documentación</th>
                </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade modal-doc" id="docModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="docModalTitle"><i class="bi bi-file-earmark-richtext me-2"></i>Documentación del proyecto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body doc-modal-body" id="docModalContent">
                <div class="doc-state">Selecciona un proyecto para cargar la documentación.</div>
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

    function renderLoadingState() {
        $('#docModalContent').html(
            '<div class="doc-state flex-column">' +
            '<div class="spinner-border text-info" role="status" aria-hidden="true"></div>' +
            '<p class="mt-3 mb-0">Cargando documentación...</p>' +
            '</div>'
        );
    }

    function renderErrorState(message) {
        $('#docModalContent').html(
            '<div class="doc-state">' +
            '<div class="w-100">' +
            '<i class="bi bi-exclamation-triangle-fill fs-2 text-warning"></i>' +
            '<p class="mt-2 mb-0">' + message + '</p>' +
            '</div>' +
            '</div>'
        );
    }

    $('#projectsTable').DataTable({
        ajax: basePath + '/api/projects',
        columns: [
            { data: 'name' },
            {
                data: 'project_url',
                render: function (data) {
                    return '<a href="' + data + '" target="_blank" rel="noopener">' + data + '</a>';
                }
            },
            {
                data: null,
                orderable: false,
                searchable: false,
                render: function (row) {
                    return '<button class="btn btn-sm btn-view-doc" data-id="' + row.id + '" data-project="' + $('<div>').text(row.name).html() + '"><i class="bi bi-eye-fill me-1"></i>Ver</button>';
                }
            }
        ],
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/es-ES.json'
        }
    });

    $(document).on('click', '.btn-view-doc', function () {
        var id = $(this).data('id');
        var projectName = $(this).data('project') || 'Documentación del proyecto';
        var modalElement = document.getElementById('docModal');
        var modal = bootstrap.Modal.getOrCreateInstance(modalElement);

        $('#docModalTitle').html('<i class="bi bi-file-earmark-richtext me-2"></i>' + projectName);
        renderLoadingState();
        modal.show();

        $.getJSON(basePath + '/api/projects/' + encodeURIComponent(id) + '/doc')
            .done(function (response) {
                if (response && response.success && response.html) {
                    $('#docModalContent').html(response.html);
                    return;
                }

                if (response && response.html) {
                    $('#docModalContent').html(response.html);
                    return;
                }

                renderErrorState((response && response.message) ? response.message : 'No fue posible cargar la documentación.');
            })
            .fail(function () {
                renderErrorState('No se pudo cargar la documentación en este momento.');
            });
    });
});
</script>
<?php
$scripts = ob_get_clean();
include __DIR__ . '/layout.php';
