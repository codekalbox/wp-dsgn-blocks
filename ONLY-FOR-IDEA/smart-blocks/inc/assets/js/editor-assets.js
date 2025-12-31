(function (window, wp) {

    // check if gutenberg's editor root element is present.
    const editorEl = document.getElementById('editor');
    if (!editorEl) {
        return;
    }

    const importBtn = `<div class="sb-import-button">
        <button id="sb-template-import-button" class="components-button components-icon-button" title="Smart Blocks">
            Design Template
        </button>
    </div>`;

    const unsubscribe = wp.data.subscribe(() => {
        setTimeout(() => {
            if (!document.getElementById("sb-template-import-button")) {
                const toolbalEl = editorEl.querySelector('.edit-post-header-toolbar__left') ?? editorEl.querySelector('.editor-document-tools__left');
                if (toolbalEl instanceof HTMLElement) {
                    toolbalEl.insertAdjacentHTML('beforeend', importBtn);
                    document.getElementById("sb-template-import-button").addEventListener("click", () => {
                        const templateImportBlock = wp.blocks.createBlock("smart-blocks/template-import", {isLibraryOpen: !0});
                        wp.data.dispatch("core/block-editor").insertBlocks(templateImportBlock);
                    });
                }
            }
        }, 1)
    });

})(window, wp)