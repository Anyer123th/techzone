const productosData=[
{nombre:"Laptop",precio:45000,img:"laptop.jpg"},
{nombre:"Mouse",precio:850,img:"mouse.jpg"},
{nombre:"Teclado",precio:1200,img:"teclado.png"},
{nombre:"Monitor",precio:9500,img:"monitor.jpg"},
{nombre:"Audífonos",precio:1800,img:"Aud#U00edfonos.png"},
{nombre:"Bocina",precio:2200,img:"Bocina.png"},
{nombre:"Cámara",precio:15500,img:"camara.png"},
{nombre:"Router WiFi",precio:2100,img:"router.jpg"},
{nombre:"Memoria USB",precio:450,img:"Memoria USB.png"},
{nombre:"Disco duro",precio:3200,img:"Disco duro.png"},
{nombre:"Webcam",precio:1700,img:"webcam.jpg"},
{nombre:"Impresora",precio:12500,img:"Impresora.png"}
];

let carrito=JSON.parse(localStorage.getItem("carrito"))||[];
function guardarCarrito(){localStorage.setItem("carrito",JSON.stringify(carrito));}
function toggleCarrito(){document.getElementById("panelCarrito").classList.toggle("activo");document.getElementById("overlay").classList.toggle("activo");}
function cerrarCarrito(){document.getElementById("panelCarrito").classList.remove("activo");document.getElementById("overlay").classList.remove("activo");}
function cargarProductos(lista){let cont=document.getElementById("productos");cont.innerHTML="";lista.forEach(p=>{cont.innerHTML+=`<div class="producto"><img src="img/${encodeURIComponent(p.img)}"><h3>${p.nombre}</h3><p>$${p.precio}</p><button onclick="agregarAlCarrito('${p.nombre}',${p.precio})">Agregar</button></div>`;});}
function filtrarProductos(){let texto=document.getElementById("buscador").value.toLowerCase();let filtrados=productosData.filter(p=>p.nombre.toLowerCase().includes(texto));cargarProductos(filtrados);}
function actualizarContador(){document.getElementById("contador").textContent=carrito.length;}
function agregarAlCarrito(n,p){carrito.push({nombre:n,precio:p});guardarCarrito();mostrarCarrito();}
function mostrarCarrito(){let lista=document.getElementById("listaCarrito");let totalElemento=document.getElementById("total");lista.innerHTML="";let total=0;carrito.forEach((item,i)=>{lista.innerHTML+=`<li>${item.nombre} - $${item.precio} <button onclick="eliminarProducto(${i})">X</button></li>`;total+=item.precio;});totalElemento.textContent=total;actualizarContador();}
function eliminarProducto(i){carrito.splice(i,1);guardarCarrito();mostrarCarrito();}
function vaciarCarrito(){carrito=[];guardarCarrito();mostrarCarrito();document.getElementById("factura").innerHTML="";}
function mostrarFactura(){let factura=document.getElementById("factura");let total=carrito.reduce((s,p)=>s+p.precio,0);let html="<h3>🧾 Factura</h3>";carrito.forEach(p=>html+=`<p>${p.nombre} - $${p.precio}</p>`);html+=`<strong>Total: $${total}</strong><br><button onclick="pagar()">Pagar</button>`;factura.innerHTML=html;}
function pagar(){document.getElementById("pantallaPago").classList.add("activo");vaciarCarrito();cerrarCarrito();}
function cerrarPago(){document.getElementById("pantallaPago").classList.remove("activo");}

cargarProductos(productosData);
mostrarCarrito();
