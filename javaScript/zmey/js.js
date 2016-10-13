/**
 * Created by Porhun on 04.03.2015.
 */

function crMatrix(){
    var matrix = document.getElementById('matrix');
    var n = 20*20;
    for (var i=0; i<n; i++){
        var div = document.createElement('div');
        div.className = 'cell';
        matrix.appendChild(div);
    }
}

function getCell(row, col){

}

function setCell(row, col, val){

}

window.onload = function(){
    crMatrix();
    var o = [1,2], p = [o[0],o[1]];
    if(o[0] == 1){
        alert(p);
    }
};



function var_dump(obj){
    var s = '<h1>' + obj + '</h1>';
    s += '<ol>';
    for (p in obj)
        s += '<li>' + p + ' : ' + obj[p] + '</li>';

    s += '</ol>';
    window.document.body.innerHTML = s;
}
