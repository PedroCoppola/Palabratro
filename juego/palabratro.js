
let palabras = [];
let palabraSecreta;
let intentos = [];
let maxIntentos = 6;
let filaActual = 0;
let input;
let backgroundImage;
let size = 70;
let spacing = 7;
let boton;
let angle = 0;
let palabrasValidas = new Set();


// Cargar el fondo
function preload() {
  backgroundImage = loadImage('fondo.jpg');
    loadJSON("palabras_5letras.json", data => {
    palabrasValidas = new Set(data.map(p => p.toUpperCase()));
  });
}
async function cargarPalabraSecreta() {
  try {
    const response = await fetch("get_palabras.php");
    palabras = await response.json(); // array de objetos o strings
    console.log("Palabras cargadas:", palabras);

    // Si el array son objetos tipo {id, palabra}
    const randomIndex = Math.floor(Math.random() * palabras.length);
    const palabraActual = palabras[randomIndex];

    // Si palabraActual es un objeto {id, palabra}
    window.ID_PALABRA_ACTUAL = palabraActual.id;
    window.palabraSecreta = palabraActual.palabra;

    // Si palabraActual es solo un string, usar esto:
    // window.ID_PALABRA_ACTUAL = randomIndex;
    // window.palabraSecreta = palabraActual;

    palabraSecreta = window.palabraSecreta;
    console.log("Palabra secreta:", palabraSecreta);
  } catch (error) {
    console.error("Error cargando palabras:", error);
  }
}


// ---------------------
// FUNCIONES DRAW & SETUP
// ---------------------

function setup() {
  // Crear el lienzo del tamaño de la ventana
  let cnv = createCanvas(1000, 600);
  textAlign(CENTER, CENTER);
  cnv.parent('game-container'); // ahora el canvas se mete dentro de ese div

  textSize(32);

  cargarPalabraSecreta();

  // Input para escribir palabra con estilo
  input = createInput();
  input.position(310, 475);
  input.attribute('maxlength', '5');

  // Estilo “tipo botón/cuadrado”
  input.style('text-transform', 'uppercase');
  input.style('font-size', '32px');
  input.size(765, 50);
  input.style('text-align', 'center');
  input.style('border-radius', '12px');
  input.style('border', '3px dashed #868686ff');  // borde visible
  input.style('background', '#f0f0f0ff');         // color similar al botón correcto
  input.style('color', '#003388');              // texto azul oscuro
  input.style('font-family', "'Gloria Hallelujah', cursive");
  input.style('font-weight', 'bold');
  input.style('box-shadow', '3px 3px 0 #000');
  input.style('outline', 'none');               // quita el outline por defecto al hacer focus

  // Opcional: hover/focus estilo más “activo”
  input.elt.addEventListener('focus', () => {
    input.style('background', '#e8f4ff');
    input.style('box-shadow', '6px 6px 0 #000');
  });

  // Botón para probar palabra
  boton = createButton("Probar");

  boton.position(310, 540); boton.size(775, 50);

  // estilos base
  boton.style('font-size', '20px');
  boton.style('font-weight', 'bolder');
  boton.style('border', '3px dashed #003388');
  boton.style('background', '#cce5ff');
  boton.style('color', '#003388');
  boton.style('border-radius', '12px');
  boton.style('box-shadow', '3px 3px 0 #000');
  boton.style('font-family', "'Gloria Hallelujah', cursive");
  boton.mousePressed(probarPalabra);


  // Optional: also handle global p5 key presses
  window.keyPressed = function() {
    if (keyCode === ENTER) {
      probarPalabra();
    }
  };

  let btnRestart = select("#btn-restart");
  btnRestart.mousePressed(() => {
    location.reload(); // recarga la página y arranca de nuevo
  });

}

function draw() {
  angle += 0.015; // más lento
  let rot = sin(angle) * 1.1;     // rotación muy leve (-1.5° a 1.5°)
  let skewX = cos(angle * 0.7) * 2; // skew más chico (-2° a 2°)
  let floatY = sin(angle * 1.5) * 2; // flotado sutil (-2px a 2px)

  boton.style(
    "transform",
    `translateY(${floatY}px) rotate(${rot}deg) skewX(${skewX}deg)`
  );

  // Mostrar intentos en varias filas
  for (let i = 0; i < intentos.length; i++) {
    intentos[i].mostrar();
  }

  // Mensajes finales después de agotar intentos o ganar
  if (filaActual >= maxIntentos && !gano()) {
    noLoop();
    mostrarOverlay(false, palabraSecreta);
  }
  if (gano()) {
    noLoop();
    mostrarOverlay(true, palabraSecreta);
    if (!window.puntajeGuardado) { // para que no lo guarde varias veces
      window.puntajeGuardado = true;

      let { puntaje, monedas } = calcularPuntaje(palabraSecreta, intentos);
      guardarResultado(ID_USUARIO_ACTUAL, puntaje, monedas);
    }
  }

}

// ---------------------
// FUNCIONES VARIAS
// ---------------------

function probarPalabra() {
  if (filaActual >= maxIntentos || gano()) return;

  let palabra = input.value().toUpperCase();

  // 🔹 Validar longitud
  if (palabra.length !== 5) {
    alert("La palabra debe tener 5 letras");
    return;
  }

  // 🔹 Validar existencia en diccionario
  if (!palabrasValidas.has(palabra)) {
    alert("Esa palabra no existe 😅");
    return;
  }

  // 🔹 Registrar intento
  let fila = new Fila(filaActual, palabra, palabraSecreta);
  intentos.push(fila);
  filaActual++;
  input.value("");

  // 🔹 Caso GANÓ
  if (fila.esCorrecta()) {
    if (!window.resultadoGuardado) {
      window.resultadoGuardado = true;
      const attemptsUsed = filaActual; // ya incrementado
      const res = calcularPuntaje(palabraSecreta, intentos, attemptsUsed, maxIntentos);

      guardarResultado(
        ID_USUARIO_ACTUAL,
        palabraSecreta,
        true,               // adivinada
        attemptsUsed,
        res.puntaje,
        res.monedas,
        ""
      );
    }
    return;
  }
}

function gano() {
  return intentos.some(fila => fila.esCorrecta());
}

function mostrarOverlay(ganaste, palabraSecreta) {
  let overlay = select("#overlay");
  let titulo = select("#overlay-titulo");
  let mensaje = select("#overlay-mensaje");

  if (ganaste) {
    titulo.html("🎉 ¡Ganaste!");
    mensaje.html("La palabra era <b>" + palabraSecreta + "</b>");
  } else {
    titulo.html("😭 Perdiste");
    mensaje.html("La palabra era <b>" + palabraSecreta + "</b>");
  }

  overlay.style("display", "flex"); // mostrar overlay
}


function calcularPuntaje(palabraSecreta, intentos, attemptsUsed, maxIntentos) {
  // letras únicas de la palabra secreta
  const letrasSecreta = new Set(palabraSecreta.split(''));
  const uniqueSecret = letrasSecreta.size;

  // letras únicas que usó el jugador en todos los intentos hasta ahora
  const used = new Set();
  for (let fila of intentos) {
    for (let c of fila.cuadrados) {
      if (c.letra && c.letra.trim() !== '') used.add(c.letra);
    }
  }
  const uniqueUsed = used.size;

  // base y penalización (ajustá constantes si querés)
  const base = Math.max(1, uniqueSecret * 100);
  const penalty = Math.max(0, uniqueUsed - uniqueSecret) * 20;

  // multiplicador por rapidez: si adivinás en intento 1 => multiplicador alto.
  const multiplicador = Math.max(1, (maxIntentos - attemptsUsed + 1)); // 6 -> 1..6

  let puntaje = Math.round(Math.max(20, (base - penalty) * multiplicador));
  // monedas: sencillo y balanceado: 2 monedas por nivel de multiplicador
  let monedas = Math.max(0, Math.round(multiplicador * 2));

  return { puntaje, monedas, uniqueSecret, uniqueUsed, multiplicador };
}


function guardarResultado(idUsuario, palabraSecreta, adivinada, intentosUsados, puntaje, monedas, pistas) {
  if (!idUsuario) {
    console.log("Usuario no logueado: no se guarda en BD.");
    return;
  }

  fetch('guardar_resultado.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      idUsuario: idUsuario,
      palabra: palabraSecreta,
      adivinada: adivinada ? 1 : 0,
      intentos: intentosUsados,
      pistas_usadas: pistas || '',
      puntaje: puntaje,
      monedas: monedas
    })
  })
  .then(r => r.json())
  .then(json => {
    if (json.ok) {
      console.log("Guardado OK", json);
      // opcional: actualizar UI de monedas/puntaje en pantalla
      if (json.nuevo_puntaje !== undefined) {
        document.getElementById('puntaje').textContent = json.nuevo_puntaje;
      }
      if (json.nuevas_monedas !== undefined) {
        // actualizar si tenés un span para monedas
        const mEl = document.getElementById('monedas');
        if (mEl) mEl.textContent = json.nuevas_monedas;
      }
    } else {
      console.warn("Error guardando:", json.error);
    }
  })
  .catch(err => console.error("Fetch error guardar_resultado:", err));
}



// ---------------------
// CLASES
// ---------------------

class Fila {
  constructor(numFila, palabra, palabraSecreta) {
    this.numFila = numFila;
    this.cuadrados = [];

    // Centrado en X
    let totalWidth = 5 * size + 4 * spacing;
    let startX = width / 2 - totalWidth / 2;
    let startY = numFila * (size + spacing);

    // Paso 1: contar letras de la palabra secreta
    let stock = {};
    for (let i = 0; i < palabraSecreta.length; i++) {
      let letra = palabraSecreta[i];
      stock[letra] = (stock[letra] || 0) + 1;
    }

    // Paso 2: marcar correctas primero
    let estados = new Array(5).fill("gris");
    for (let i = 0; i < 5; i++) {
      if (palabra[i] === palabraSecreta[i]) {
        estados[i] = "correcto";
        stock[palabra[i]]--;
      }
    }

    // Paso 3: marcar presentes (amarillo) si quedan en stock
    for (let i = 0; i < 5; i++) {
      if (estados[i] === "gris") {
        let letra = palabra[i];
        if (stock[letra] > 0) {
          estados[i] = "presente";
          stock[letra]--;
        } else {
          estados[i] = "incorrecto"; // rojo salmón
        }
      }
    }

    // Crear cuadrados
    for (let i = 0; i < 5; i++) {
      let letra = palabra[i];
      let x = startX + i * (size + spacing);
      let y = startY;
      this.cuadrados.push(new Cuadrado(x, y, letra, estados[i]));
    }
  }

  mostrar() {
    this.cuadrados.forEach(c => c.mostrar());
  }

  esCorrecta() {
    return this.cuadrados.every(c => c.estado === "correcto");
  }
}

class Cuadrado {
  constructor(x, y, letra, estado) {
    this.x = x;
    this.y = y;
    this.size = size;
    this.letra = letra;
    this.estado = estado; // "correcto", "presente", "gris", "incorrecto"
    this.angleOffset = random(TWO_PI); // cada cuadrado flota distinto
  }

  mostrar() {
    push();

    // Animación flotado y rotación
    let t = millis() / 1000;
    let rot = sin(t + this.angleOffset) * 0.03; // rotación leve (~1.5°)
    let floatY = sin(t * 1.5 + this.angleOffset) * 2; // flotado sutil
    translate(this.x + this.size / 2, this.y + this.size / 2 + floatY);
    rotate(rot);

    // --- Cuadrado con color y sombra ---
    rectMode(CENTER);
    fill(this.colorSegunEstado());
    noStroke();
    rect(0, 0, this.size, this.size, 12);

    // Sombra cartoon
    fill(0, 20); // sombra negra semi-transparente
    rect(3, 3, this.size, this.size, 12);

    // Letra centrada
    fill(this.textColorSegunEstado());
    textFont('Gloria Hallelujah');
    textStyle(BOLD);
    textSize(this.size * 0.5);
    textAlign(CENTER, CENTER);
    text(this.letra, 0, 0);

    pop();
  }

  colorSegunEstado() {
    switch (this.estado) {
      case "correcto": return color(204, 229, 255); // celeste claro
      case "presente": return color(255, 234, 97);   // amarillo
      case "gris": return color(219, 219, 208);      // gris
      case "incorrecto": return color(255, 130, 120); // rojo salmón
    }
  }

  textColorSegunEstado() {
    switch (this.estado) {
      case "gris": return color(0);      // negro para letras grises
      default: return color(0, 51, 136);   // azul oscuro
    }
  }

  setLetra(letra) {
    this.letra = letra;
  }

  setEstado(estado) {
    this.estado = estado;
  }
}

async function comprarPista(idPista) {
  if (!ID_USUARIO_ACTUAL) {
    alert("Tenés que iniciar sesión para usar las pistas.");
    return;
  }

  const formData = new FormData();
  formData.append("id_pista", idPista);

  try {
    const res = await fetch("comprar_pista.php", { method: "POST", body: formData });
    const data = await res.json();

    if (!data.ok) {
      alert("⚠️ " + data.error);
      return;
    }

    // Actualizar monedas visualmente
    document.getElementById("monedas").innerText = data.nuevas_monedas;

    // Ejecutar la acción específica
    ejecutarPista(data.tipo_accion);

  } catch (err) {
    console.error("Error comprando pista:", err);
  }
}

function ejecutarPista(tipo) {
  switch (tipo) {
    case "revelar_primera":
      revelarLetra(0);
      break;

    case "revelar_ultima":
      revelarLetra(palabraSecreta.length - 1);
      break;

    case "mostrar_longitud":
      alert("📏 La palabra tiene " + palabraSecreta.length + " letras.");
      break;

    case "revelar_mitad":
      const mitad = Math.floor(palabraSecreta.length / 2);
      alert("🧩 Mitad revelada: " + palabraSecreta.slice(0, mitad));
      break;

    default:
      console.warn("Tipo de pista no reconocido:", tipo);
  }
}

// Ejemplo auxiliar
function revelarLetra(pos) {
  const letra = palabraSecreta[pos].toUpperCase();
  alert(`🔤 La letra ${pos + 1} es "${letra}"`);
}

document.querySelectorAll('.btn-comprar').forEach(btn => {
  btn.addEventListener('click', async () => {
    const pistaItem = btn.closest('.pista-item');
    const pistaId = pistaItem.dataset.id;
    const precio = parseInt(pistaItem.dataset.precio, 10);
    const nombre = pistaItem.querySelector('h4').innerText;

    if (!confirm(`¿Comprar "${nombre}" por ${precio} monedas?`)) return;

    // palabraSecreta: variable global en tu juego p5.js
    // Asegurate de exponerla a scope global si está dentro de un archivo (ej: window.palabraSecreta = palabraSecreta;)
    const palabraActual = typeof palabraSecreta !== 'undefined' ? palabraSecreta : null;

    const body = new URLSearchParams();
    body.append('pista_id', pistaId);
    if (palabraActual) body.append('palabra', palabraActual);

    try {
      const res = await fetch('comprar_pista.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: body.toString()
      });

      const data = await res.json();

      if (!data.ok) {
        alert(`⚠️ ${data.error}`);
        return;
      }

      // Actualizar monedas UI
      const monedasEl = document.getElementById('monedas');
      if (monedasEl) monedasEl.innerText = data.nuevas_monedas;

      // Obtener el valor real devuelto por PHP
      const valorRef = data.valor_referencia;

      // Construir la card con valor real si está disponible, si no mostrar la columna
      const cont = document.getElementById('contenedor-pistas-compradas');
      const card = document.createElement('div');
      card.classList.add('pista-card');

      let detalleHTML = '';
      if (valorRef !== null && valorRef !== '') {
        // Mostrar el valor real. Si es 'palabra' o similar, estilizar
        detalleHTML = `<p><strong>Resultado:</strong> ${escapeHtml(String(valorRef))}</p>`;
      } else {
        detalleHTML = `<p><i>Referencia:</i> ${escapeHtml(data.pista.columna)} (no disponible)</p>`;
      }

      card.innerHTML = `
        <h4>${escapeHtml(data.pista.icono || '💡')} ${escapeHtml(data.pista.nombre)}</h4>
        <p>${escapeHtml(data.pista.descripcion)}</p>
        ${detalleHTML}
      `;
      cont.appendChild(card);

      alert(`✅ ${data.msg}`);
    } catch (err) {
      console.error(err);
      alert('❌ Error al conectar con el servidor.');
    }
  });
});

// pequeña función para escapar HTML (seguridad)
function escapeHtml(s) {
  return s
    .replaceAll('&', '&amp;')
    .replaceAll('<', '&lt;')
    .replaceAll('>', '&gt;')
    .replaceAll('"', '&quot;')
    .replaceAll("'", '&#039;');
}


// cuando cargás la palabra
window.ID_PALABRA_ACTUAL = palabraActual.id; // o como se llame tu propiedad
window.palabraSecreta = palabraActual.palabra; // si querés seguir usando esto también

