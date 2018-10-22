<script type="text/html" id="tmpl-content-template">
    <div class="container tender-card">
        <div class="card">
            <h5 class="card-header">{{data.post.post_title}}</h5>
            <div class="card-body">
                <p><strong>Вид: </strong>{{data.terms.name}}</p>
                <p class="card-text">{{{data.post.post_content}}}   </p>
                <a href="{{{data.post.permalink}}}" class="btn btn-primary">
                    Детальніше
                </a>
            </div>
        </div>
    </div>
</script>
