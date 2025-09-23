
// Vector de palabras que pueden salir
let palabras = [];
let palabraSecreta;
let intentos = [];
let maxIntentos = 6;
let filaActual = 0;
let input;
let backgroundImage;
let size = 80; 
let spacing = 10; 
let boton;
let angle = 0;

// Cargar el fondo
  function preload() {
      backgroundImage = loadImage('fondo.jpg');
    }

    async function cargarPalabraSecreta() {
  try {
    const response = await fetch("get_palabras.php");
    palabras = await response.json(); // array de strings
    console.log("Palabras cargadas:", palabras);

    // elegir una palabra random
    const randomIndex = Math.floor(Math.random() * palabras.length);
    palabraSecreta = palabras[randomIndex];
    console.log("Palabra secreta:", palabraSecreta);
  } catch (error) {
    console.error("Error cargando palabras:", error);
  }
}


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
    text("No adivinaste nada wachin 😭", width / 2, height / 2);
  }
  if (gano()) {
    text("¡Adivinaste! 🎉", width / 2, height / 2);
  }
}
function probarPalabra() {
  if (filaActual >= maxIntentos || gano()) return;

  let palabra = input.value().toUpperCase();
  if (palabra.length !== 5) {
    alert("La palabra debe tener 5 letras");
    return;
  }

  let fila = new Fila(filaActual, palabra, palabraSecreta);
  intentos.push(fila);
  filaActual++;
  input.value("");
}

function gano() {
  return intentos.some(fila => fila.esCorrecta());
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
    let startY =  numFila * (size + spacing);

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
    switch(this.estado){
      case "correcto": return color(204, 229, 255); // celeste claro
      case "presente": return color(255, 234, 97);   // amarillo
      case "gris": return color(219, 219, 208);      // gris
      case "incorrecto": return color(255, 130, 120); // rojo salmón
    }
  }

  textColorSegunEstado() {
    switch(this.estado){
      case "gris": return color(0);      // negro para letras grises
      default: return color(0,51,136);   // azul oscuro
    }
  }

  setLetra(letra) {
    this.letra = letra;
  }

  setEstado(estado) {
    this.estado = estado;
  }
}

