<script>
    console.error("<?=$code?> <?=$type?>\nMessage: <?=$message?>");
</script>
<div class="page-wrap d-flex flex-row align-items-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12 text-center">
                <span class="display-1 d-block"><?=$code?></span>
                <div class="mb-4 lead">Oops! We found an error.</div>
                <a href="/<?=ROOT_DIR?>/" class="btn btn-link">Back to Home</a>
            </div>
        </div>
    </div>
</div>