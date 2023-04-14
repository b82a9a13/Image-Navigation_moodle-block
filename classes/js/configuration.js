let height = []
let width = []
let ratio = []
const roles = ['admin', 'coach', 'learner']
for(i = 0; i < roles.length; i++){
    ratio[roles[i]] = (document.getElementById(`${roles[i]}_aspectratio`).checked === true) ? 'object-fit:contain;' : ''
    height[roles[i]] = document.getElementById(`${roles[i]}_image_height`).value
    width[roles[i]] = document.getElementById(`${roles[i]}_image_width`).value
}
function navbtn(string){
    const div = document.getElementById(string+'_nav_div')
    div.style.display = (div.style.display == 'none') ? 'block' : 'none'
}
function newimage(string){
    const adminimages = document.querySelectorAll('.'+string+'_image')
    const div = document.createElement("div")
    div.className = string+'_image image-div-inner'
    const number = adminimages.length + 1
    div.id = string+'_image_'+number
    const position = document.createElement('h3')
    position.innerText = `${number}`
    div.appendChild(position)
    const image = document.createElement('img')
    image.id = string+'_the_image'+number
    image.className = 'imagenav'
    image.setAttribute('style', 'width: '+width[string]+'px; height:'+height[string]+'px;'+ratio[string])
    div.appendChild(image)
    const div3 = document.createElement('div')
    const input = document.createElement("input")
    input.type = 'file'
    input.name = 'file'+number
    input.id = string+'_file'+number
    input.className = string+'-files'
    input.setAttribute('onchange', "newfile('"+string+"', "+number+")")
    div3.appendChild(input)
    const div2 = document.createElement("div")
    div2.className = "input-link"
    const p = document.createElement("p")
    p.innerText = 'Input a url'
    const input2 = document.createElement("input")
    input2.type = 'url'
    input2.name = 'link'+number
    input2.id = string+'_link'+number
    input2.className = string+'-links'
    input2.setAttribute('onchange', "newurl('"+string+"', "+number+")")
    const delbtn = document.createElement("button")
    delbtn.innerText = 'Delete'
    delbtn.type = 'button'
    delbtn.id = string+'_delbtn'+number
    delbtn.className = 'btn-danger btn'
    delbtn.setAttribute('onclick', "deleteimage('"+string+"',"+number+")")
    p.appendChild(input2)
    div2.appendChild(p)
    div3.appendChild(div2)
    div.appendChild(div3)
    div.appendChild(delbtn)
    document.getElementById(string+'_image_div').appendChild(div)
    document.getElementById(string+'_image_total').setAttribute("value", number)
    refreshpreview(string)
}
function newfile(string, number){
    const file = document.getElementById(string+'_file'+number).files[0]
    if(file){
        const fileReader = new FileReader()
        fileReader.readAsDataURL(file)
        fileReader.addEventListener("load", function(){
            document.getElementById(string+'_the_image'+number).setAttribute('src', this.result)
            refreshpreview(string)
        })
    } else {
        document.getElementById(string+'_the_image'+number).setAttribute('src', '')
        refreshpreview(string)
    }
}
function newurl(string, number){
    const url = document.getElementById(`${string}_link${number}`)
    if(url.value){
        url.setAttribute('isnew','true')
    }
}
function resize(string){
    const images = document.querySelectorAll('.'+string+'_imagenav')
    const length = images.length+1
    for(let i = 1; i < length; i++){
        document.getElementById(string+'_the_image'+i).setAttribute('style', 'width:'+width[string]+"px;height:"+height[string]+"px;"+ratio[string])
    }
}
function widthchange(string){
    const value = document.getElementById(string+'_image_width')
    value.setAttribute('isnew', 'true')
    width[string] = value.value
    resize(string)
    refreshpreview(string)
}
function heightchange(string){
    const value = document.getElementById(string+'_image_height')
    value.setAttribute('isnew', 'true')
    height[string] = value.value
    resize(string)
    refreshpreview(string)
}
function ratiochange(string){
    const value = document.getElementById(string+'_aspectratio')
    value.setAttribute('isnew', 'true')
    ratio[string] = (value.checked) ? 'object-fit:contain;' : ''
    resize(string)
    refreshpreview(string)
}
function previewclick(string){
    const div = document.getElementById(string+'_preview_div')
    if(div.style.display == 'block'){
        div.style.display = 'none'
    } else {
        div.style.display = 'block'
        updatepreview(string)
    }
}
function refreshpreview(string){
    const div = document.getElementById(string+'_preview_div')
    if(div.style.display == 'block'){
        updatepreview(string)
    }
}
function updatepreview(string){
    const div = document.getElementById(string+'_preview_div')
    div.innerHTML = ''
    let images = document.querySelectorAll('.'+string+'_imagenav')
    let number = images.length+1
    for(let i = 1; i < number; i++){
        const image = document.createElement('img')
        image.setAttribute('src', images[i-1].src)
        image.setAttribute('style', 'width:'+width[string]+'px;height:'+height[string]+'px;'+ratio[string])
        image.className = string+'_preview_img preview_img'
        image.id = string+'_preview_img'+i
        image.setAttribute('onclick', "window.open('"+document.getElementById(string+'_link'+i).value+"')")
        div.appendChild(image)
    }
}
function submitdata(string){
    const imageWidth = document.getElementById(string+'_image_width').value
    let newNav = true
    let error = false
    let errorMessage = 'Invalid: '
    let pos = 0;
    if(imageWidth.length == 0 || imageWidth < 1){
        errorMessage += 'Width'
        error = true
        pos++;
    } 
    const imageHeight = document.getElementById(string+'_image_height').value
    if(imageHeight.length == 0 || imageHeight < 1){
        if(pos > 0){
            errorMessage += ', '
        }
        errorMessage += 'Height'
        pos++
        error = true
    }
    const images = document.querySelectorAll('.'+string+'_image')
    let number = images.length + 1
    for(let i = 1; i < number; i++){
        if(document.getElementById(string+"_the_image"+i).getAttribute('src') != '' && document.getElementById(string+'_file'+i).value == ''){
            newNav = false
        } 
        else {
            const file = document.getElementById(string+'_file'+i)
            const fileType = file.value.split(".")
            if(file.files.length == 0 || fileType[fileType.length - 1] != 'png' && fileType[fileType.length - 1] != 'jpg'){
                if(pos > 0){
                    errorMessage += ', '
                }
                errorMessage += 'File '+i
                pos++
                error = true
            }
        }
        const link = document.getElementById(string+'_link'+i)
        if(link.value.length == 0 || !link.value.includes('https://') && !link.value.includes('http://')){
            if(pos > 0){
                errorMessage += ', '
            }
            errorMessage += 'Url '+i
            pos++
            error = true
        }
    }
    if(error === false){
        if(newNav === true){
            document.getElementById(string+'_form').submit()
            document.getElementById(string+'_success').style.display = 'none'
            document.getElementById(string+'_success_message').innerText = 'Success'
        } else{
            formsubmit(string)
        }
    } else if(error === true){
        document.getElementById(string+'_error_message').innerText = errorMessage
        document.getElementById(string+'_error').style.display = 'block'
        document.getElementById(string+'_success').style.display = 'none'
    }
}
function formsubmit(string){
    document.getElementById(string+'_error').style.display = 'none'
    const images = document.querySelectorAll(`.${string}_image`)
    let number = images.length + 1
    let formData = new FormData()
    let fileNum = 0
    let linkNum = 0
    let change = false
    for(let i = 1; i < number; i++){
        const image = document.getElementById(`${string}_the_image${i}`)
        const file = document.getElementById(`${string}_file${i}`)
        if(image.getAttribute('src').length > 0 && file.value == ''){
        } else {
            change = true
            formData.append(`file${fileNum}`, file.files[0])
            formData.append(`file${fileNum}id`, i)
            fileNum++
        }
        const link = document.getElementById(string+'_link'+i)
        if(link.getAttribute('isnew') == 'true'){
            change = true
            formData.append(`link${linkNum}`, link.value)
            formData.append(`link${linkNum}id`, i)
            linkNum++
        }
    }
    formData.append('fileTotal', fileNum)
    formData.append('linkTotal', linkNum)
    const width = document.getElementById(`${string}_image_width`)
    if(width.getAttribute('isnew') == 'true'){
        change = true
        formData.append(`width`, width.value)
    }
    const height = document.getElementById(`${string}_image_height`)
    if(height.getAttribute('isnew') == 'true'){
        change = true
        formData.append(`height`, height.value)
    }
    let aspectratio = document.getElementById(`${string}_aspectratio`)
    if(aspectratio.getAttribute('isnew') == 'true'){
        change = true
        if(aspectratio.checked === true){
            formData.append(`aspectratio`, `true`)
        } else {
            formData.append(`aspectratio`, `false`)
        }
    }
    formData.append('role', string)
    if(change === true){
        fxhr = new XMLHttpRequest()
        fxhr.open('POST', './classes/inc/update.inc.php')
        fxhr.onload = function(){
            if(this.status == 200){
                const text = JSON.parse(this.responseText)
                let error = false;
                let errorMessage = 'Invalid: '
                let pos = 0
                if(text['width']){
                    if(text['width'] === true){
                        errorMessage += 'Width'
                        error = true
                        pos++
                    }
                }
                if(text['height']){
                    if(text['height'] === true){
                        if(pos > 0){
                            errorMessage += ', '
                        }
                        errorMessage += 'Height'
                        error = true
                        pos++
                    }
                }
                if(text['aspectratio']){
                    if(text['aspectratio'] === true){
                        if(pos > 0){
                            errorMessage += ', '
                        }
                        errorMessage += 'Aspect Ratio'
                        error = true
                        pos++
                    }
                }
                if(text['link']){
                    for(let i = 0; i < text['link'].length; i++){
                        if(pos > 0){
                            errorMessage += ', '
                        }
                        errorMessage += `Link ${text['link'][i]}`
                        error = true
                        pos++
                    }
                }
                if(text['file']){
                    for(let i = 0; i < text['file'].length; i++){
                        if(pos > 0){
                            errorMessage += ', '
                        }
                        errorMessage += `File ${text['file'][i]}`
                        error = true
                        pos++
                    }
                }
                if(text['role']){
                    if(text['role'] === true){
                        if(pos > 0){
                            errorMessage += ', '
                        }
                        errorMessage += `Role`
                        error = true
                        pos++
                    }
                }
                if(error === true){
                    document.getElementById(string+'_error_message').innerText = errorMessage
                    document.getElementById(string+'_error').style.display = 'block'            
                    document.getElementById(string+'_success').style.display = 'none'
                } else if(error === false){
                    document.getElementById(string+'_error').style.display = 'none'            
                    if(text['success'] === true){
                        document.getElementById(string+'_success_message').innerText = 'Success'
                        document.getElementById(string+'_success').style.display = 'block'
                    }
                }
            }
        }
        fxhr.send(formData)
    }
}
function deleteimage(string, number){
    const image = document.getElementById(string+'_the_image'+number)
    const input = document.getElementById(string+'_link'+number)
    if(image.src == '' && input.value == ''){
        const totalImages = document.querySelectorAll("."+string+"_image").length
        const maxValue = document.getElementById(string+'_image_total').getAttribute('value');
        if(totalImages == number){
            document.getElementById(string+'_image_'+number).remove()
            document.getElementById(string+'_image_total').setAttribute('value', maxValue-1)
        } else if(totalImages > number){
            document.getElementById(string+'_image_'+number).remove()
            const afterNum = totalImages - number;
            for(let i = 1; i < (afterNum+1); i++){
                const div = document.getElementById(string+'_image_'+(maxValue-(i-1)))
                div.querySelector('h3').innerText = maxValue - i
                div.querySelector('img').id = string+"_the_image"+(maxValue-i)
                div.id = string+'_image_'+(maxValue-i)
                const fileInput = document.getElementById(string+"_file"+(maxValue-(i-1)))
                fileInput.setAttribute('onchange', "newfile('"+string+"', "+(maxValue-i)+")")
                fileInput.name = 'file'+(maxValue-i)
                fileInput.id = string+"_file"+(maxValue-i)
                const linkInput = document.getElementById(string+'_link'+(maxValue-(i-1)))
                linkInput.name = 'link'+(maxValue-i)
                linkInput.setAttribute('onchange', "newurl('"+string+"',"+(maxValue-i)+")")
                linkInput.id = string+"_link"+(maxValue-i)
                const delbtn = document.getElementById(string+'_delbtn'+(maxValue-(i-1)))
                delbtn.setAttribute('onclick', "deleteimage('"+string+"',"+(maxValue-i)+")")
                delbtn.id = string+"_delbtn"+(maxValue-i)
            }
            document.getElementById(string+'_image_total').setAttribute('value', maxValue-afterNum)
        }
    } else{
        xhr = new XMLHttpRequest()
        xhr.open('POST', './classes/inc/delete.inc.php')
        xhr.onload = function(){
            if(this.status == 200){
                const text = JSON.parse(this.responseText)

            }
        }
        xhr.send('role='+string+'&id='+number);
    }
}