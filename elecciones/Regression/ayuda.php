<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Ayuda</title>
</head>

<body>
<h1>Análisis de regresión</h1>
<div>
  <p><strong>Introducción:</strong></p>
  <p>Autor: Cogito, ergo sum</p>
  <p>El análisis de regresión es una de las herramientas centrales de la estadística que ayuda a investigar las relaciones entre variables. Si solo hay una variable explicativa, se denomina “regresión simple”. A menudo es difícil encontrar situaciones de este tipo en la vida real. Calcular una regresión simple implica ajustar una línea recta a través del gráfico de dispersión obtenido de la muestra. De los conceptos básicos de la geometría de coordenadas, recuerde que una línea recta tiene la ecuación:</p>
  <blockquote>
    <p>y=c + mx</p>
    <p>donde m = pendiente de la línea y c es la intersección con el eje y.</p>
  </blockquote>
  <p>Por supuesto, puede haber muchas líneas rectas a través de un diagrama de dispersión. El análisis de regresión elige UNA línea en particular: el criterio utilizado es minimizar <a href="http://mathworld.wolfram.com/LeastSquaresFitting.html" target="_blank">la suma de cuadrados de los errores (distancia desde los puntos en el diagrama de dispersión hasta la línea construida).</a></p>
  <p>Si hay más de una variable de la que depende la predicción, se denomina &quot;Regresión múltiple&quot;: tiene en cuenta múltiples factores adicionales (por separado) para que se pueda evaluar el efecto de cada variable (independiente) sobre la variable dependiente.</p>
  <p>La ecuación para una regresión múltiple (lineal) se puede expresar como:</p>
  <blockquote>
    <p>y = a+bx1+cx2+…</p>
  </blockquote>
  <p>Matemáticamente, es idéntico a la &quot;Regresión simple&quot;. Por ejemplo, para resolver una regresión de 2 parámetros, necesitamos 3 dimensiones; por lo tanto, en lugar de estimar una línea recta, seleccionamos un solo “plano”, nuevamente de modo que la suma de los cuadrados de los errores sea mínima.</p>
  <p>De hecho, podemos extrapolar esto a un número arbitrariamente grande de variables independientes. Afortunadamente, las computadoras no tienen problemas para procesar números usando álgebra matricial para resolver numerosas ecuaciones simultáneas y llegar a los resultados.</p>
  <p><strong>Uso de álgebra matricial para resolver regresión múltiple:</strong></p>
  <p>Sea X la matriz de datos de las variables predictoras (independientes).<br />
    Sea Y el vector de datos que representa la variable criterio (dependiente).<br />
    y Sea 'b' el vector de datos que representa los coeficientes de regresión.</p>
  <p>La fórmula para calcular b (matriz de coeficientes) usando álgebra matricial viene dada por la siguiente fórmula:</p>
  <blockquote>
    <p>b = (X'X) -1 X'Y</p>
  </blockquote>
  <p>La prueba de esta ecuación es bastante simple:</p>
  <p>1. La ecuación simplificada para una regresión lineal (simple) en términos matriciales es Y=Xb+e<br />
    2. Supongamos que el error promedio (e) será igual a 0. La ecuación se convierte en Y=Xb y necesitamos encontrar el valor de ' b'<br />
    3. multiplica ambos lados de la ecuación por X' (transposición de X): X'Y = X'Xb<br />
    4. Estamos tratando de deshacernos de X'X en el lado derecho... así que multiplica ambos lados de la ecuación con (X'X) -1 – la inversa de X'X:<br />
    (X'X) -1 X'Y = (X'X) -1 (X'X)b<br />
    5. Cualquier matriz multiplicada por su inversa es la matriz identidad I:<br />
    (X'X) -1 X'Y = Ib<br />
    6. Ib = <strong>b = (X'X) -1 X'Y</strong></p>
  <blockquote>
    <p>[Nota: la ecuación anterior contiene la matriz <a href="http://mathworld.wolfram.com/Moore-PenroseMatrixInverse.html" target="_blank">pseudoinversa de Moore-Penrose</a> que garantiza que se produzcan inversas en matrices CUADRADOS. A pseudo-1 =(A'A) -1 *A'</p>
    <p>Para resolver la ecuación Y = Xb, la solución ingenua sería multiplicar ambos lados por X -1<br />
      X -1 Y = X -1 Xb, y así<br />
      X -1 Y = b</p>
    <p>Sin embargo, recuerde que las inversas de matrices sólo se definen en matrices cuadradas. No podemos garantizar que la matriz de variables independientes sea una matriz cuadrada... de hecho, ¡nunca lo es! el tamaño de la muestra debe ser razonable para garantizar resultados correctos. El uso de una matriz pseudoinversa resuelve muy bien este problema.]</p>
  </blockquote>
  <p><strong>Ejemplo:</strong></p>
  <div>
    <div id="atatags-26942-603846" data-adtags-width="700"></div>
  </div>
  <p>Un flujo de trabajo típico que involucra análisis de regresión es el siguiente:</p>
  <ol>
    <li>Formular una hipótesis sobre la relación entre variables de interés.</li>
    <li>Recopilar datos de una muestra representativa.</li>
    <li>Calcular los parámetros de regresión y probar (o refutar) la hipótesis.</li>
  </ol>
  <p>Un ejemplo común de análisis de regresión múltiple es el de admisiones de estudiantes universitarios. El comité de admisiones basa su decisión en numerosos factores que cree que influirán en el GPA de los estudiantes.</p>
  <p>Por ejemplo, una universidad podría plantear la hipótesis de que el GPA que obtendrá el estudiante admitido depende de su GPA de la escuela secundaria, puntaje del SAT (V + Q) y cartas de recomendación (por supuesto, la solidez de las cartas de recomendación deberá ser cuantificado)</p>
  <p>Luego se encuesta a una muestra de estudiantes que actualmente asisten a la universidad para determinar su GPA universitario actual, GPA de la escuela secundaria, puntajes del SAT y fortaleza de las cartas de recomendación.</p>
  <p>La ecuación predictiva del GPA universitario (variable dependiente) es:</p>
  <blockquote>
    <p>Y' = a+b1X1+b2X2+b3X3</p>
  </blockquote>
  <p>donde Y' = GPA previsto<br />
    {b1, b2, b3} = Coeficientes de regresión<br />
    a = Intercepción<br />
    X1 = GPA HS, X2 = Puntuación SAT y X3 = Fuerza de las cartas de recomendación</p>
  <p>Entonces, una vez que se determinan los coeficientes y la intersección, es bastante fácil ingresar valores para X1, X2, X3 y “predecir” el GPA futuro de un estudiante.</p>
  <p><strong>Implementación:</strong></p>
  <p>Aunque existen paquetes estadísticos numéricos que pueden calcular fácilmente parámetros de regresión, todos están orientados al cálculo &quot;fuera de línea&quot;. Es decir, primero se recopilan datos y luego se procesan los números para llegar al resultado. Recientemente estuve involucrado en un proyecto que requería aritmética de regresión en línea, es decir, mostrar los parámetros de regresión relevantes al final de una encuesta en línea. Decidí escribir mi propia biblioteca PHP para este propósito (el código fuente completo se adjunta al final de este blog).</p>
  <p>Cada observación se puede escribir como una ecuación de la siguiente manera:</p>
  <p><img src="imagenes_ayuda/1.png" width="318" height="159" /></p>
  <div>
    <div id="atatags-26942-75985" data-adtags-width="700"></div>
  </div>
  <p>Para fines explicativos, considere una regresión lineal simple. En términos matriciales, la ecuación queda así:</p>
  <p><img src="imagenes_ayuda/2.png" width="568" height="389" /></p>
  <p>Luego podemos resolver {b0,b1}... Observe cómo la matriz X debe llenarse <strong>con unos en la primera columna</strong> . El mismo enfoque exacto también funciona para la regresión múltiple: ¡solo usamos matrices más grandes!</p>
  <p><img src="imagenes_ayuda/3.png" width="291" height="167" /></p>
  <p><img src="imagenes_ayuda/4.png" width="281" height="189" /></p>
  <p>'n' es el tamaño de la muestra (número de observaciones)</p>
  <p>Consulte el documento de Word adjunto en la descarga para obtener una derivación detallada de por qué resolver las ecuaciones matriciales anteriores nos da la suma de los errores mínimos cuadrados.</p>
  <p>Las diversas fórmulas utilizadas son:</p>
  <ol>
    <li>b = (X'X) -1 X'Y (Coeficientes de regresión. Esta es una matriz nX1)</li>
    <li>SSR = b'X'Y – (1/n) (Y'UU'Y) (suma de cuadrados debido a la regresión – esto es un escalar. U es un vector unitario de dimensiones nX1)</li>
    <li>SSE = Y'Y-b'X'Y (Suma de cuadrados por errores – escalar)</li>
    <li>SSTO = SSR+SSE (Suma total de cuadrados – escalar)</li>
    <li>dfTotal = tamaño_muestra – 1 (Grados de libertad totales)</li>
    <li>dfModel = num_independent – ​​1 (grados de libertad del modelo)</li>
    <li>dfResidual = dfTotal – dfModel (Grados de libertad residuales)</li>
    <li>MSE = SSE/dfResidual (Error cuadrático medio – escalar)</li>
    <li>SE = (X'X) -1 *(MSE) luego toma la raíz cuadrada de los elementos en la diagonal</li>
    <li>t-stat = b[i][j]/SE[i][j]</li>
    <li>R 2 = SSR/SSTO</li>
    <li>F = (SSR/dfModel)/(SSE/dfResidual)</li>
  </ol>
  <p>El corazón de la biblioteca es la clase Lib_Matrix. Maneja todas las operaciones de manipulación de matrices requeridas. Siempre que fue posible, se ha proporcionado una interfaz fluida para que el código sea más intuitivo y legible. El cálculo de la regresión se realiza en la clase &quot;Lib_Regression&quot;. También adjunté el conjunto completo de pruebas unitarias (100% de cobertura de código) para ayudarlo a comprender mejor la API.</p>
</div>
</body>
</html>
