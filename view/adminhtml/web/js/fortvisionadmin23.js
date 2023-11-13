const checkIfInitFortvision = () => {
    // let rt=new URL(window.location.href)
    // let url=rt.protocol+'//'+rt.host+'/index.php/rest/V1/fortvision/getList/'
    // fetch(url).then(e=>(e.json())).then(data=>{
    //     console.log('loaded',data)
    // })
    // console.log('aava')
    const ifenabledvalue0 = document.getElementById("fortvision_platform_general_is_enabled")
    const ifenabledvalue = ifenabledvalue0?ifenabledvalue0.value:false

    const ifenabled = document.getElementById("row_fortvision_platform_general_is_enabled")
    let ifenabledcode = ifenabled ? ifenabled.innerHTML:false
  //  const sel = document.querySelector('#row_fortvision_platform_general_longareapublisher')

    let magentoid = false
    const magentoLine = document.getElementById('row_fortvision_platform_general_magento_id')
  //  console.log('magentoLine',magentoLine,ifenabledvalue)
    if (ifenabledvalue && magentoLine) {
        let valueStorage = magentoLine.querySelector('.control-value')
        if (valueStorage) {
            magentoid = valueStorage.innerText
            const addCode= `<tr><td colspan="4"><suprematik-page magentoid="${magentoid}" settings="${window.btoa(JSON.stringify({magentoid: magentoid}))}"></suprematik-page></td></tr>`+
                (ifenabledcode? `<table style="width:100%"><tr>${ifenabledcode}</tr></table>`:'')
            magentoLine.parentElement.parentElement.innerHTML=addCode

        }
    }
  //  const value = document.getElementById('row_fortvision_platform_general_magento_id'

///    document.getElementById('row_fortvision_platform_general_magento_id').querySelector('.control-value').innerText
  //  let val = '{}'
   // if (value) val = value.value
/*    if (sel && sel.parentElement && sel.parentElement.parentElement && sel.parentElement.parentElement.parentElement) {
        const src=sel.parentElement.parentElement.parentElement.parentElement.innerHTML
        const addCode= `<suprematik-page settings="${window.btoa(JSON.stringify({values: val}))}"></suprematik-page>` +
            (ifenabledcode? `<table style="width:100%"><tr>${ifenabledcode}</tr></table>`:'')
  //      sel.parentElement.parentElement.parentElement.parentElement.innerHTML =addCode
    //    console.log('SOURCE',ifenabledcode,src,addCode )

    } */
 //   console.log('AVV100', sel, value);
}

document.onreadystatechange = () => {
    // console.log('docdoc')
    if (document.readyState === "interactive") {
        checkIfInitFortvision();
    }
};
