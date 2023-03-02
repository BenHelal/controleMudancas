$(document).ready(function () {
    $('#mudancas_client').chosen({});
});
{% if gestor %}
$(document).ready(function () {
    const box = document.getElementById('nanseForm');
    if (document.getElementById('mudancasgestor_to_app_nansenName').value == '') {
        document.getElementById("nansen").checked = false;
        const box = document.getElementById('nanseForm');
        if (document.getElementById('nansen').checked) {
            box.style.display = 'block';
        } else {
            box.style.display = 'none';
        }
    } else {
        document.getElementById("nansen").checked = true;
        const box = document.getElementById('nanseForm');
        if (document.getElementById('nansen').checked) {
            box.style.display = 'block';
        } else {
            box.style.display = 'none';
        }
    }
}); {% endif %} {% if gestor != true and manager != true %} $(document).ready(function () {
    $('#mudancasgestor_to_app_areaImpact').chosen({});
});
$(document).ready(function () {
    $('#mudancas_areaImpact').chosen({});
});
$(document).ready(function () {
    $('#mudancas_areaResp').chosen({});
});

{% elseif gestor != true and manager == true %}
$(document).ready(function () {
    $('#mudancas_manager_areaImpact').chosen({});
});
$(document).ready(function () {
    $('#mudancas_manager_areaResp').chosen({});
});
$(document).ready(function () {
    $('#mudancas_manager_mangerMudancas').chosen({});
});

//mudancas_client

{% elseif mangerOfAreaDidntApp == true %}


{% else %}
$(document).ready(function () {
    $('#mudancasgestor_to_app_areaImpact').chosen({});
});
$(document).ready(function () {
    $('#mudancasgestor_to_app_areaResp').chosen({});
});
$(document).ready(function () {
    $('#mudancasgestor_to_app_mangerMudancas').chosen({});
}); {% endif %}


{% if gestor != true and manager == true %}

/*mudancas_manager_client*/
const mudancas_manager_comMan = document.getElementById('mudancas_manager_comMan');
mudancas_manager_comMan.disabled = true; {% if  m.mangerMudancas != null %} const mudancas_manager_mangerMudancas = document.getElementById('mudancas_manager_mangerMudancas');
mudancas_manager_mangerMudancas.disabled = true;
const mudancas_manager_appMan = document.getElementById('mudancas_manager_appMan');
mudancas_manager_appMan.disabled = true;
mudancas_manager_comMan.disabled = true; {% endif %} {% if  m.comGest == null %} mudancas_manager_mangerMudancas.disabled = false;
mudancas_manager_appMan.disabled = false;
mudancas_manager_comMan.disabled = false; {% endif %} {% endif %} {% if  creation == 'false' %} {% if m.comMan == null %}
{% elseif m.comMan != null and m.comGest == null %}
var form = document.getElementById("test");
var elements = form.elements;
for (var i = 0, len = 10; i < len; ++i) {
    elements[i].readOnly = true;
}
{% else %}
var form = document.getElementById("test");
var elements = form.elements;
for (var i = 0, len = 9; i < len; ++i) {
    elements[i].readOnly = true;
}
{% endif %}
{% if gestor %}
var nodes = document.getElementById("selectAreaResp").getElementsByTagName('*');
for (var i = 0; i < nodes.length; i++) {
    nodes[i].disabled = true;
}


var nodes = document.getElementById("gestorMudancas").getElementsByTagName('*');
for (var i = 0; i < nodes.length; i++) {
    nodes[i].disabled = true;
}
{% endif %}

{% endif %}

{% if manager and(gestor == false) %}

$(document).ready(function () {
    const box = document.getElementById('nanseForm');
    if (document.getElementById('mudancas_manager_nansenName').value == '') {
        document.getElementById("nansen").checked = false;
        const box = document.getElementById('nanseForm');
        if (document.getElementById('nansen').checked) {
            box.style.display = 'block';
        } else {
            box.style.display = 'none';
        }
    } else {
        document.getElementById("nansen").checked = true;
        const box = document.getElementById('nanseForm');
        if (document.getElementById('nansen').checked) {
            box.style.display = 'block';
        } else {
            box.style.display = 'none';
        }
    }
});

function validate() {
    const box = document.getElementById('nanseForm');
    if (document.getElementById('nansen').checked) {
        box.style.display = 'block';
    } else {
        document.getElementById('mudancas_manager_nansenName').value = '';
        document.getElementById('mudancas_manager_nansenNumber').value = '';
        box.style.display = 'none';
    }
}

{% elseif gestor %}
$(document).ready(function () {
    const box = document.getElementById('nanseForm');
    if (document.getElementById('mudancasgestor_nansenName').value == '') {
        document.getElementById("nansen").checked = false;
        const box = document.getElementById('nanseForm');

        if (document.getElementById('nansen').checked) {
            box.style.display = 'block';
        } else {
            box.style.display = 'none';
        }
    } else {
        document.getElementById("nansen").checked = true;
        const box = document.getElementById('nanseForm');
        if (document.getElementById('nansen').checked) {
            box.style.display = 'block';
        } else {
            box.style.display = 'none';
        }
    }
});

function validate() {
    const box = document.getElementById('nanseForm');
    if (document.getElementById('nansen').checked) {
        box.style.display = 'block';
    } else {
        box.style.display = 'none';
    }
}

new TomSelect("#mudancasgestor_areaImpact", { maxItems: 36 });
{% else %}
$(document).ready(function () {
    const box = document.getElementById('nanseForm');
    if (document.getElementById('mudancas_nansenName').value == '') {
        document.getElementById("nansen").checked = false;
        const box = document.getElementById('nanseForm');
        if (document.getElementById('nansen').checked) {
            box.style.display = 'block';
        } else {
            box.style.display = 'none';
        }
    } else {
        document.getElementById("nansen").checked = true;
        const box = document.getElementById('nanseForm');
        if (document.getElementById('nansen').checked) {
            box.style.display = 'block';
        } else {
            box.style.display = 'none';
        }
    }
});

function validate() {
    const box = document.getElementById('nanseForm');
    if (document.getElementById('nansen').checked) {
        box.style.display = 'block';
    } else {
        document.getElementById('mudancas_nansenName').value = '';
        document.getElementById('mudancas_nansenNumber').value = '';
        box.style.display = 'none';
    }
}
{% endif %}