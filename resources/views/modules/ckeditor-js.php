<script src="<?= e(WT_CKEDITOR_BASE_URL) ?>ckeditor.js"></script>
<script src="<?= e(WT_CKEDITOR_BASE_URL) ?>adapters/jquery.js"></script>

<script>
var CKEDITOR_BASEPATH = <?= json_encode(WT_CKEDITOR_BASE_URL) ?>;

// Enable for all browsers
CKEDITOR.env.isCompatible = true;

// Disable toolbars
CKEDITOR.config.removePlugins = "forms,newpage,preview,print,save,templates";
CKEDITOR.config.extraAllowedContent = "area[shape,coords,href,target,alt,title];map[name];img[usemap];*[class,style]";

// Activate the editor
$(".html-edit").ckeditor(function(config){config.removePlugins = "forms";}, {
	language: "<?= strtolower(WT_LOCALE) ?>"
});
</script>
