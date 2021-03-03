<head>
    <link rel="stylesheet" type="text/css" href="/<?=$_ENV["BASENAME"]?>/public/assets/style/files.css">
</head>

<style>
    #grid{
        display: grid;
        grid-template-columns: 25% 25% 25% 25%;
        padding: 5px;
    }
    .drop{
        font-size: 1.5em;
        color: black;
        text-decoration: none;
    }
    .icon{
        color: black;
        font-size: 3rem;
    }
    @media (max-width: 700px){
        #grid{
            grid-template-columns: 35% 35% 35%;
        }
        .icon{
            font-size: 2rem;
        }
        .drop{
            font-size: 1em;
        }
    }
</style>

<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/flow.js/2.14.1/flow.min.js"></script> -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/plupload/3.1.2/plupload.full.min.js"></script>

<!-- <div id="container">
    <a id="pickfiles" href="javascript:;">[Select files]</a>
    <a id="uploadfiles" href="javascript:;">[Upload files]</a>
</div> -->
<ul class="nav nav-pills">
        <li>
            <?php if($curent != "") { ?> <a href="/<?=$_ENV["BASENAME"]?>/filesystem/browse/<?=substr($curent, 0, strrpos($curent, "\\"));?>" style="font-size: 1.5em;"><i class="fas fa-chevron-circle-left"></i></a> <?php }
            else { ?><a href="/<?=$_ENV["BASENAME"]?>/" style="font-size: 1.5em;"><i class="fas fa-chevron-circle-left"></i></a> <?php }?>
        </li>
            <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">File</a>
            <div class="dropdown-menu">
                <a class="dropdown-item" onclick="let nName = prompt('New name: '); if(nName != null) location.replace(`/<?=$_ENV["BASENAME"]?>/filesystem/createfile/<?=str_replace('\\', '/', $curent);?>/${nName}`);">New File</a>
                <a class="dropdown-item" onclick="let nName = prompt('New name: '); if(nName != null) location.replace(`/<?=$_ENV["BASENAME"]?>/filesystem/createdir/<?=str_replace('\\', '/', $curent);?>/${nName}`);">New Folder</a>
                <div class="dropdown-divider"></div>
                <!-- onclick="popup_content('popup_wrap', 'show')" -->
                <a class="dropdown-item" id="pickfiles" href="javascript:;" >Upload</a>
            </div>
            </li>
    </ul>
    <div id="grid" id="main">
         <?php foreach($contents as $file) { ?>
            <div class="file">
                <?php 
                // Make sure only admin's can acces system
                if($file["name"] == "SYSTEM" && $isAdmin == true/* $isAdmin for testing */) return; 
                switch(strtolower($file["ext"])){
                    case 'txt': 
                    ?><a class="icon" href="/<?=$_ENV["BASENAME"]?>/filesystem/edit/<?=$curent?>/<?=$file["name"]?>"><i class="far fa-file-alt"></i></a><?php
                        break;
                    case 'html': 
                        ?><a class="icon" href="/<?=$_ENV["BASENAME"]?>/filesystem/showfile/<?=$curent?>/<?=$file["name"]?>"><i class="far fa-file-code"></i></a><?php
                    break;
                    case 'js': 
                    case 'cs': 
                    case 'c': 
                    case 'h':
                    case 'cpp':
                    case 'cpp': 
                    case 'py': 
                    case 'json': 
                    case 'php': 
                    case 'sql': 
                    ?><a class="icon" href="/<?=$_ENV["BASENAME"]?>/filesystem/edit/<?=$curent?>/<?=$file["name"]?>"><i class="far fa-file-code"></i></a><?php
                        break;
                    case 'pdf': 
                    ?><a class="icon" href="/<?=$_ENV["BASENAME"]?>/filesystem/showfile/<?=$curent?>/<?=$file["name"]?>"><i class="far fa-file-pdf"></i></a><?php
                        break;
                    case 'docx':
                        ?><a class="icon" href="/<?=$_ENV["BASENAME"]?>/filesystem/showfile/<?=$curent?>/<?=$file["name"]?>"><i class="far fa-file-word"></i></a><?php
                        break;
                    case 'xls':
                        ?><a class="icon" href="/<?=$_ENV["BASENAME"]?>/filesystem/showfile/<?=$curent?>/<?=$file["name"]?>"><i class="far fa-file-excel"></i></a><?php
                        break;
                    case 'powerpoint':
                        ?><a class="icon" href="/<?=$_ENV["BASENAME"]?>/filesystem/showfile/<?=$curent?>/<?=$file["name"]?>"><i class="far fa-file-powerpoint"></i></a><?php
                        break;
                    case 'png': 
                    case 'jpg': 
                    case 'jpeg': 
                    ?>
                        <a class="icon" href="/<?=$_ENV["BASENAME"]?>/filesystem/showfile/<?=$curent?>/<?=$file["name"]?>"><i class="far fa-file-image"></i></a>
                    <?php break;
                    case 'mp3': ?>
                        <a class="icon" href="/<?=$_ENV["BASENAME"]?>/filesystem/showfile/<?=$curent?>/<?=$file["name"]?>"><i class="far fa-file-audio"></i></a>
                    <?php break;
                    case 'mp4': 
                    case 'mov':
                    case 'wmv':
                    case 'avi':
                    ?>
                        <a class="icon" href="/<?=$_ENV["BASENAME"]?>/filesystem/showfile/<?=$curent?>/<?=$file["name"]?>"><i class="far fa-file-video"></i></a>
                    <?php break;
                    case "zip":
                    case "rar":?>
                        <a class="icon" href="/<?=$_ENV["BASENAME"]?>/filesystem/download/<?=$curent?>/<?=$file["name"]?>"><i class="far fa-file-archive"></i></a>
                    <?php break;
                    case 'app': ?>
                        <a class="icon" href="/<?=$_ENV["BASENAME"]?>/filesystem/browse/<?=$curent?>/<?=$file["name"]?>"><i class="fas fa-server"></i></a>
                    <?php break;
                    case null: //dir ?>
                        <a class="icon" href="/<?=$_ENV["BASENAME"]?>/filesystem/browse/<?=$curent?>/<?=$file["name"]?>"><i class="far fa-folder"></i></a>
                    <?php break;
                    default: ?>
                        <a class="icon" href="/<?=$_ENV["BASENAME"]?>/filesystem/showfile/<?=$curent?>/<?=$file["name"]?>"><i class="far fa-file"></i></a>
                    <?php break; } ?>
                    <a class="dropdown-toggle drop" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?= $file["name"] ?></a>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <!-- File -->
                            <?php if($file["ext"] != null) { ?>
                            <a class="dropdown-item"  href="/<?=$_ENV["BASENAME"]?>/filesystem/edit/<?=$curent?>/<?=$file["name"]?>"><i class="fas fa-edit"></i>Edit</a>
                            <a class="dropdown-item" href="/<?=$_ENV["BASENAME"]?>/filesystem/download/<?=$curent?>/<?=$file["name"]?>"><i class="fas fa-download">Download</i></a>
                            <?php } ?>
                        
                            <a class="dropdown-item" href="/<?=$_ENV["BASENAME"]?>/filesystem/delete/<?=$curent?>/<?=$file["name"]?>?path=<?=$curent?>/<?=$file["name"]?>"><i class="fas fa-trash-alt"></i>Remove</a>
                            <a class="dropdown-item" onclick="let nName = prompt('New name: '); location.replace(`/<?=$_ENV["BASENAME"]?>/filesystem/rename/<?=$curent?>/<?=$file["name"]?>/${nName}`);"><i class="fas fa-edit"></i>Rename</a>
                        
                            <?php if($file["ext"] == null) { ?>
                                <!-- <a class="dropdown-item" href="/<?=$_ENV["BASENAME"]?>/filesystem/zip/<?=$curent?>/<?=$file["name"]?>"><i class="far fa-file-archive">Zip</i></a> -->
                            <?php } else if($file["ext"] == "zip" || $file["ext"] == "rar") {?>
                                <!-- <a class="dropdown-item" href="/<?=$_ENV["BASENAME"]?>/filesystem/unzip/<?=$curent?>/<?=$file["name"]?>"><i class="far fa-file-archive">Unzip</i></a> -->
                        <?php } ?>
                    </div>
            <?php } ?>
        </div>
    </div>
    <div id="filelist"></div>
    <pre id="console"></pre>
    <script type="text/css">
    #popup_wrap {
        width: 100%;
        height: 100%;   
        top: 0;
        left: 0;   
        position: fixed;	
        background: rgba(0, 0, 0, 0.74);
        z-index: 9999999;
    }
    #popup_content {
        width: 50%;
        height: 300px;
        padding:20px;
        position: relative;
        top: 15%;
        left: 25%;
        background: #1b100ed9;
        border: 10px solid #00cbfe;  
    }
    </script>
    <!---Upload popup---->
    <div id="popup_wrap" style='display:none'>
        <div id="popup_content">
            <center>
                <form action="/<?=$_ENV["BASENAME"]?>/filesystem/upload/<?=$curent?>" enctype="multipart/form-data" method="POST">
                    <input type="file" class="upld-file" name="upload[]" multiple>
                    <input type="hidden" name="path" value="<?=$curent?>">
                    <button type="submit" class="btn btn-primary">Upload</button>
                </form>
            </center>
        </div>
    </div>
<!---end Upload popup---->
<!-- UPLOAD BUTTON -->
<input style="display: none;" type="button" id="upToggle" value="Pause OR Continue"/>

<!-- UPLOAD LIST -->
<div id="uplist"></div>

<script>
        var uploader = new plupload.Uploader({
            runtimes: 'html5,html4',
            browse_button: 'pickfiles',
            url: '/<?=$_ENV["BASENAME"]?>/filesystem/upload/<?=str_replace('\\', '/', $curent);?>',
            chunk_size: '10mb',
            //container: document.getElementById('container'), // ... or DOM Element itself
            
            /*filters : {
                max_file_size : '10mb',
                mime_types: [
                    {title : "Image files", extensions : "jpg,gif,png"},
                    {title : "Zip files", extensions : "zip"}
                ]
            },*/
            init: {
                PostInit: function () {
                    document.getElementById('filelist').innerHTML = '';
                },
                FilesAdded: function (up, files) {
                    plupload.each(files, function (file) {
                    document.getElementById('filelist').innerHTML += `<div id="${file.id}">${file.name} (${plupload.formatSize(file.size)}) <strong></strong></div>`;
                    });
                    uploader.start();
                },
                UploadProgress: function (up, file) {
                    document.querySelector(`#${file.id} strong`).innerHTML = `<span>${file.percent}%</span>`;
                },
                Error: function (up, err) {
                    alert("Error when uploading!");
                    console.log(err);
                },
                UploadComplete: function(uploader, files)
                {
                    location.reload();
                }
            }
        });
        uploader.init();
</script>
