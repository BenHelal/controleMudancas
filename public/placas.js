var i = 0;
var array = ["OK", "FALHA"];
var i = 0;
if (i === 0) {
    document.getElementById("btnSub").style.display = 'none';
}
function myFunction() {
    i++;
    const div = document.createElement("div");
    div.setAttribute('id', i + 'div');
    div.setAttribute('class', 'col-md-3 mb-3');
    document.getElementById('test').appendChild(div);

    const label = document.createElement('label');
    label.setAttribute('for', i);
    label.innerHTML = "Código 2D Serdia : ";
    document.getElementById(i + 'div').appendChild(label);
//'class':'form-control'
    const node = document.createElement("input");
    node.setAttribute('class', 'form-control');
    node.setAttribute('id', i);
    node.setAttribute('name', i);
    document.getElementById(i + 'div').appendChild(node);

    const div2 = document.createElement("div");
    div2.setAttribute('id', i + 'div2');
    div2.setAttribute('class', 'col-md-3 mb-3');
    document.getElementById('test').appendChild(div2);

    const label2 = document.createElement('label');
    label2.setAttribute('for', i + 'test');
    label2.innerHTML = "Teste Placaddds : ";
    document.getElementById(i + 'div2').appendChild(label2);

    const node2 = document.createElement("select");
    node2.setAttribute('id', i + 'test');
    node2.setAttribute('class', 'custom-select');
    node2.setAttribute('name', i + 'test');
    document.getElementById(i + 'div2').appendChild(node2);

    for (var j = 0; j < array.length; j++) {
        var option = document.createElement("option");
        option.value = array[j];
        option.text = array[j];
        node2.appendChild(option);
    }
    const div3 = document.createElement("div");
    div3.setAttribute('id', i + 'div3');
    div3.setAttribute('class', 'col-md-6 mb-3');
    document.getElementById('test').appendChild(div3);

    const label3 = document.createElement('label');
    label3.setAttribute('for', i + 'desc');
    label3.innerHTML = "Descrição da Falha : ";
    document.getElementById(i + 'div3').appendChild(label3);

    const br = document.createElement('br');
    document.getElementById(i + 'div3').appendChild(br);

    const node3 = document.createElement("input");
    node3.setAttribute('id', i + 'desc');
    node3.setAttribute('name', i + 'desc');
    node3.setAttribute('class', 'form-control');
    document.getElementById(i + 'div3').appendChild(node3);

    if (i === 1) {
        document.getElementById("btnSub").style.display = 'inline';
    }

}