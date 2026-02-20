<?php

declare(strict_types=1);
ob_start();
?>
<nav class="navbar topbar-premium sticky-top">
    <div class="container-fluid px-4 py-2">
        <div class="d-flex align-items-center gap-3">
            <span class="brand-mark"><i class="bi bi-grid-1x2-fill"></i></span>
            <div>
                <div class="fw-bold">Repositorio Proyectos PASS</div>
                <small class="text-light opacity-75">Panel corporativo de documentación y contenido técnico</small>
            </div>
        </div>
        <div class="d-flex align-items-center gap-3">
            <span class="user-chip"><i class="bi bi-person-circle me-1"></i><?php echo htmlspecialchars((string) ($user['username'] ?? 'N/A'), ENT_QUOTES, 'UTF-8'); ?></span>
            <a href="<?php echo htmlspecialchars($basePath . '/logout', ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-outline-light btn-sm topbar-exit"><i class="bi bi-box-arrow-right me-1"></i>Salir</a>
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

<div class="modal fade modal-doc" id="viewerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header viewer-header sticky-top">
                <div>
                    <h5 class="modal-title mb-1" id="viewerModalTitle">Viewer de proyecto</h5>
                    <span class="viewer-badge" id="viewerTypeBadge">DOC</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="#" id="viewerDownload" class="btn btn-sm btn-outline-info d-none" target="_blank" rel="noopener"><i class="bi bi-download me-1"></i>Descargar</a>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
            </div>
            <div class="modal-body doc-modal-body" id="viewerModalContent">
                <div class="doc-state">Selecciona un proyecto para cargar el contenido.</div>
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

    function escapeHtml(value) {
        return $('<div>').text(value || '').html();
    }

    function setViewerHeader(projectName, typeLabel) {
        $('#viewerModalTitle').html('<i class="bi bi-stars me-2"></i>' + escapeHtml(projectName));
        $('#viewerTypeBadge').text(typeLabel.toUpperCase());
    }

    function setDownload(url, enabled) {
        var $download = $('#viewerDownload');
        if (enabled && url) {
            $download.attr('href', url).removeClass('d-none');
            return;
        }

        $download.attr('href', '#').addClass('d-none');
    }

    function renderLoadingState() {
        $('#viewerModalContent').html(
            '<div class="doc-state flex-column">' +
            '<div class="spinner-border text-info" role="status" aria-hidden="true"></div>' +
            '<p class="mt-3 mb-0">Cargando contenido...</p>' +
            '</div>'
        );
    }

    function renderErrorState(message) {
        $('#viewerModalContent').html(
            '<div class="viewer-error">' +
            '<i class="bi bi-exclamation-triangle-fill"></i>' +
            '<h6>No se pudo cargar el contenido</h6>' +
            '<p>' + escapeHtml(message || 'Error inesperado.') + '</p>' +
            '</div>'
        );
    }

    function openViewer() {
        var modalElement = document.getElementById('viewerModal');
        var modal = bootstrap.Modal.getOrCreateInstance(modalElement);
        modal.show();
    }

    function requestViewer(endpoint, typeLabel, projectName, mode) {
        setViewerHeader(projectName, typeLabel);
        setDownload('', false);
        renderLoadingState();
        openViewer();

        $.getJSON(endpoint)
            .done(function (response) {
                if (mode === 'video') {
                    if (response && response.videoUrl) {
                        $('#viewerModalContent').html(
                            '<div class="video-frame">' +
                                '<video controls preload="metadata" class="w-100" src="' + escapeHtml(response.videoUrl) + '"></video>' +
                            '</div>'
                        );
                        setDownload(response.videoUrl, true);
                        return;
                    }

                    renderErrorState((response && response.message) ? response.message : 'Video no disponible.');
                    return;
                }

                if (response && response.html) {
                    $('#viewerModalContent').html(response.html);
                    return;
                }

                renderErrorState('No fue posible cargar el documento.');
            })
            .fail(function () {
                renderErrorState('No se pudo cargar el contenido en este momento.');
            });
    }

    $('#projectsTable').DataTable({
        ajax: basePath + '/api/projects',
        columns: [
            {
                data: null,
                render: function (row) {
                    return '<div class="project-name">' + escapeHtml(row.name) + '</div>' +
                        '<div class="project-company">' + escapeHtml(row.company || '') + '</div>';
                }
            },
            {
                data: 'project_url',
                render: function (data) {
                    var safe = escapeHtml(data);
                    return '<a href="' + safe + '" target="_blank" rel="noopener">' + safe + '</a>';
                }
            },
            {
                data: null,
                orderable: false,
                searchable: false,
                render: function (row) {
                    var project = escapeHtml(row.name);
                    return '<div class="btn-group action-group" role="group">' +
                        '<button class="btn btn-sm btn-view-doc" data-type="doc" data-id="' + row.id + '" data-project="' + project + '">Documentación</button>' +
                        '<button class="btn btn-sm btn-view-doc" data-type="func" data-id="' + row.id + '" data-project="' + project + '">Funcionalidad</button>' +
                        '<button class="btn btn-sm btn-view-doc" data-type="video" data-id="' + row.id + '" data-project="' + project + '">Video</button>' +
                        '</div>';
                }
            }
        ],
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/es-ES.json'
        }
    });

    $(document).on('click', '.btn-view-doc', function () {
        var id = $(this).data('id');
        var docType = $(this).data('type');
        var projectName = $(this).data('project') || 'Proyecto';

        if (docType === 'video') {
            requestViewer(basePath + '/api/projects/' + encodeURIComponent(id) + '/video', 'Video', projectName, 'video');
            return;
        }

        var label = docType === 'func' ? 'Funcionalidad' : 'Documentación';
        requestViewer(basePath + '/api/projects/' + encodeURIComponent(id) + '/' + docType, label, projectName, 'doc');
    });
});
</script>
<?php
$scripts = ob_get_clean();
include __DIR__ . '/layout.php';
