window.format_options_for_select_input  = (options, params = {}) =>{
    if (!Array.isArray(options) && typeof options == 'object'){
        // allow implementation of {value1: label1, value2: label2} options
        res = Object.keys(options).reduce((key, output)=>{
            output.push({
                value: key,
                label: options[key]
            })
            return output
        }, [])
    } else if (typeof options[0] !== 'object'){
        // allow implementation of [val1, val2, val3] options
        res = options.map((val)=>{
            return {value: val, label: val}
        })
    } else {
        res = options
    }
    if (params.with_all){
        res.unshift({value: 'all', label: params.all_label ?? '---'})
    }
    console.log('final options,', res)
    return res
}