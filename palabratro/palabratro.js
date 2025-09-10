// Vector de palabras que pueden salir
let palabras = ["CASAS", "PERRO", "MANGO", "JUEGO", "PLAZA"];
let palabraSecreta;
let intentos = [];
let maxIntentos = 6;
let filaActual = 0;
let input;
let backgroundImage;
let size = 80; 
let spacing = 10; 

// Cargar el fondo
  function preload() {
      backgroundImage = loadImage('fondo.jpg');
    }


function setup() {
// Crear el lienzo del tamaño de la ventana
  createCanvas(1000, 600);
  textAlign(CENTER, CENTER);
  textSize(32);

// Elegir una palabra de las posibles
  palabraSecreta = random(palabras);


  // Input para escribir palabra con estilo
  input = createInput();
  input.position(width / 2 - input.width / 2, height - 60);
  input.attribute('maxlength', '5');
  input.style('text-transform', 'uppercase');
  input.style('font-size', '32px');
  input.style('width', windowWidth / 2.1 + 'px');
  input.style('text-align', 'center');

  // Botón para probar palabra
  let boton = createButton("Probar");
  boton.position(width / 2 - boton.width / 2, height - 30);
  boton.mousePressed(probarPalabra);
}

function draw() {
  // Fondo con imagen


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

// Función para probar la palabra ingresada
function probarPalabra() {

  // Validar si se adivinó o si se agotaron los intentos
  if (filaActual >= maxIntentos || gano()) return;

  let palabra = input.value().toUpperCase();

  // Validar que tenga 5 letras
  if (palabra.length !== 5) {
    alert("La palabra debe tener 5 letras");
    return;
  }

  // Crear una nueva fila con la palabra ingresada
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
    let startY = 30 + numFila * (size + spacing);

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
  }

  mostrar() {
    stroke(0);
    strokeWeight(2);
    fill(this.colorSegunEstado());
    rect(this.x, this.y, this.size, this.size, 8);

    fill(0);
    noStroke();
    text(this.letra, this.x + this.size / 2, this.y + this.size / 2);
  }

  colorSegunEstado() {
    switch (this.estado) {
      case "correcto": return color(0, 180, 255); // celeste
      case "presente": return color(255, 220, 0); // amarillo
      case "gris": return color(180);             // gris
      case "incorrecto": return color(255, 130, 120); // rojo salmón
    }
  }
}
