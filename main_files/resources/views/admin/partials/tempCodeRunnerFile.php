function numberOnly(str) {
        let val = str.replace(/[^0-9.]/g, '');
        return parseFloat(val)
    }