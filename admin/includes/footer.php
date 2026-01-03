    <!-- Footer -->
    <footer class="py-4 text-center text-muted">
        <small>© <?= date('Y') ?> بي تاج - جميع الحقوق محفوظة</small>
    </footer>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
    // Initialize DataTables with RTL
    $(document).ready(function() {
        $('.datatable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/ar.json'
            },
            order: [[0, 'desc']],
            pageLength: 25
        });
    });

    // Delete confirmation
    function confirmDelete(url, name) {
        if (confirm('هل أنت متأكد من حذف "' + name + '"؟')) {
            window.location.href = url;
        }
    }

    // Show toast notification
    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-white bg-${type} border-0 show position-fixed bottom-0 end-0 m-3`;
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
    }
</script>

<?php if (isset($_SESSION['flash_message'])): ?>
<script>
    showToast('<?= $_SESSION['flash_message'] ?>', '<?= $_SESSION['flash_type'] ?? 'success' ?>');
</script>
<?php
    unset($_SESSION['flash_message'], $_SESSION['flash_type']);
endif;
?>

</body>
</html>
