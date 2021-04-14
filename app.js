const form = document.querySelector('#form')
const fileInput = document.querySelector('input#file-input')
const idInput = document.querySelector('input[name=id]')
const idInputError = document.querySelector('.id-input-error')
const infoInputError = document.querySelector('.info-input-error')
const sendButton = document.querySelector('button#send-button')
const loadButton = document.querySelector('button#load-button')
const downloadButton = document.querySelector('button#download-button')
const dropAreaContent = document.querySelector('div.drop-area-content')

let files = []
let copyFiles = {
    copyId: null,
    files: []
}

const uuidv4 = () => {
    // https://stackoverflow.com/a/2117523
    return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, c => {
        let r = (Math.random() * 16) | 0,
            v = c == 'x' ? r : (r & 0x3) | 0x8
        return v.toString(16)
    })
}

const download = (id, file, tar) => {
    $.ajax({
        url: 'PHP/API.php',
        type: 'POST',
        dataType: 'binary',
        xhrFields: {
            responseType: 'blob'
        },
        data: {
            action: 'download',
            id: id,
            file: file,
            tar: tar
        },
        success: function (data, status, xhr) {
            var blob = new Blob([data], { type: xhr.getResponseHeader('Content-Type') })
            var link = document.createElement('a')
            link.href = window.URL.createObjectURL(blob)
            link.download = 'download.png'
            link.click()
        }
    })
}

const deleteFile = id => {
    const index = files.findIndex(obj => obj.id === id)
    if (index >= 0) {
        files.splice(index, 1)
    } else {
        const copyIndex = copyFiles.files.findIndex(obj => obj.id === id)
        if (copyIndex >= 0) {
            copyFiles.files.splice(copyIndex, 1)
        }
    }
    renderFiles()
}

const renderFiles = () => {
    if (!files.length && !copyFiles.files.length) {
        dropAreaContent.innerHTML = ''
        dropAreaContent.innerHTML += `
            <span>Drag and drop</span>
        `
        return
    }

    // prettier-ignore
    const extensions = ["mp3", "wav", "aif", "cda", "mid", "midi", "mpa", "mkv", "ogg", "wpa", "wpl", "7z", "zip", "rar", "tar.gz", "pkg", "z", "csv", "dat", "json", "xml", "dat", "db", "dbf", "sql", "ns", "3ds", "max", "ai", "psd", "ttf", "woff", "woff2", "png", "bmp", "jpg", "jpeg", "gif", "tif", "tiff", "svg", "rss", "torrent", "ppt", "pps", "pptx", "odp", "asp", "c", "cs", "java", "jsp", "swift", "php", "hh", "go", "py", "js", "html", "xhtml", "css", "vb", "rb", "scss", "sass", "less", "jsx", "sh", "pl", "xls", "xlsx", "xlsm", "ods", "dll", "bak", "ini", "dmp", "sys", "cfg", "tmp", "icns", "doc", "docx", "log", "txt", "pdf", "avi", "mov", "mp4", "mpg", "mpeg", "mkv", "wmv", "wps", "exe"]

    dropAreaContent.innerHTML = ''
    ;[...files, ...copyFiles.files].forEach(obj => {
        const match = obj.file.name.match(/\.([0-9a-zA-Z]+$)/)

        const extension = match ? match[1] : '?'
        const isMakefile = obj.file.name.toLowerCase() === 'makefile'

        dropAreaContent.innerHTML += `
            <div class="drop-area-content-file">
                <div 
                    class="fi fi-size-md fi-${
                        match && extensions.includes(extension) ? extension : 'java'
                    }"
                    onclick="deleteFile('${obj.id}')"
                >
                    <div class="fi-content">${isMakefile ? 'make' : extension}</div>
                </div>
                <span>${obj.file.name}</span>
            </div>
        `
    })
}

fileInput.oninput = e => {
    const temp = [...e.target.files].filter(file => !files.find(obj => obj.file.name === file.name))

    files.push(
        ...temp.map(file => ({
            id: uuidv4(),
            file: file
        }))
    )

    renderFiles()
}

sendButton.onclick = () => {
    const regexName = /\s{1,}/
    const regexGroup = /^[0-9]{6}$/

    if (regexName.test(form.children.namedItem('name1').value)) {
        infoInputError.innerHTML = 'Фамилия не может содержать пробелов'
        return
    }
    if (regexName.test(form.children.namedItem('name2').value)) {
        infoInputError.innerHTML = 'Имя не может содержать пробелов'
        return
    }
    if (regexName.test(form.children.namedItem('name3').value)) {
        infoInputError.innerHTML = 'Отчество не может содержать пробелов'
        return
    }
    if (!regexGroup.test(form.children.namedItem('group').value)) {
        infoInputError.innerHTML = 'Неверно введен номер группы'
        return
    }
    if (!files.length && !copyFiles.files.length) {
        infoInputError.innerHTML = 'Не загружены файлы для отправки'
        return
    }
    infoInputError.innerHTML = ''

    const formData = new FormData(form)

    formData.append(
        'copyFiles',
        copyFiles.files.map(obj => obj.file.name)
    )
    formData.append('copyId', copyFiles.copyId)

    files.forEach(obj => {
        console.log(obj.file)
        formData.append('files[]', obj.file, obj.file.name)
    })

    $.ajax({
        url: 'PHP/API.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: (data, textStatus, request) => {
            console.log(data)
        }
    })
}

loadButton.onclick = e => {
    e.preventDefault()

    const regex = /^[0-9a-zA-Z]{32}$/

    if (!regex.test(idInput.value)) {
        idInputError.innerHTML = 'Некорректный код'
        return
    }
    idInputError.innerHTML = ''

    $.ajax({
        url: 'PHP/API.php',
        type: 'POST',
        data: {
            action: 'info',
            id: idInput.value
        },
        success: (data, textStatus, request) => {
            if (data.OK) {
                for (let key in data) {
                    if (key !== 'files' && key !== 'OK') {
                        form.children.namedItem(key).value = data[key]
                    }
                }
                copyFiles.copyId = data.id
                copyFiles.files.push(
                    ...data.files.name.map(name => ({
                        id: uuidv4(),
                        file: {
                            name: name
                        }
                    }))
                )
                renderFiles()
            }
        }
    })
}

downloadButton.onclick = () => {
    download('4rdbDpHKB5SVuJJ2tAYoMTXNHCpHKyb4', '', true)
}

renderFiles()
