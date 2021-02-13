<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.52.2/codemirror.min.css"></link>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.52.2/codemirror.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.52.2/mode/javascript/javascript.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.52.2/mode/css/css.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.52.2/mode/htmlmixed/htmlmixed.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.52.2/mode/xml/xml.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.52.2/mode/clike/clike.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.52.2/mode/php/php.min.js"></script>

<!-- Toolbar -->
<nav class="navbar navbar-default">
    <div class="container-fluid">
        <!-- File Button -->
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">File <span class="caret"></span></a>
                <ul class="dropdown-menu">
                <li><a onclick="let nName = prompt('New name: '); if(nName != null) location.replace(`/<?=$_ENV["BASENAME"]?>/filesystem/createfile/<?=str_replace('\\', '/', $curent);?>/${nName}?loc=/filesystem/edit/`);">New</a></li>
                <li role="separator" class="divider"></li>
                <li><a href="#">Open</a></li>
                <li role="separator" class="divider"></li>
                <li><a onclick="Save()">Save</a></li>
                <li><a href="#">Save As</a></li>
                </ul>
            </li>
        </ul>
    </div>
</nav>

<div id="code_block"></div>
<input id="text" type="hidden" value="<?=$contents?>">

<script>

    // Handle code mirror
    var editor = CodeMirror(document.querySelector('#code_block'), {
        lineNumbers: true,
        mode: '<?=$type?>'
    });
    editor.setValue(document.getElementById('text').value);
    document.body.removeChild(document.getElementById('text'))

    var height = document.body.clientHeight;
    var width = document.body.clientWidth;
    editor.setSize(width, height * 2);

    // Handle saving
    var saved = true;
    editor.on("change", () => {saved = false;});
    
    var isCtrl = false;
    document.onkeyup=function(e){
        if(e.keyCode == 17) isCtrl=false;
    }

    document.onkeydown=function(e){
        if(e.keyCode == 17) isCtrl=true;
        if(e.keyCode == 83 && isCtrl == true) {
            //run code for CTRL+S -- ie, save!
            Save();
            saved = true
            return false;
        }
    }
    window.onbeforeunload = function(){
        if(!saved) return 'Are you sure you want to leave?';
    };

    // Save function
    function Save()
    {
        document.getElementById('new-contents').innerHTML = editor.getValue();
        document.forms[0].submit();
    }
</script>
<form id="save-form" action="/<?=$_ENV["BASENAME"]?>/filesystem/save/<?=str_replace('\\', '/', $curent);?>/<?=$name?>?loc=/filesystem/edit/" style="display: none;" method="POST">
    <textarea type="text" id="new-contents" name="newContents">

    </textarea>
</form>