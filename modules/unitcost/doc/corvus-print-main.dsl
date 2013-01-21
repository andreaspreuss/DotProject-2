<!DOCTYPE style-sheet PUBLIC "-//James Clark//DTD DSSSL Style Sheet//EN" [
<!ENTITY dbstyle PUBLIC "-//Norman Walsh//DOCUMENT DocBook Print Stylesheet//EN" CDATA DSSSL>

]>


<style-sheet>
<style-specification use="docbook">
<style-specification-body>

;; Tipo de papel
(define %paper-type%
  "USletter")

;; Generar tabla de contenidos...
(define %generate-article-toc% 
  #t)
;; En una página separada
(define %generate-article-toc-on-titlepage%
  #f)

;; El font del cuerpo
(define %body-font-family% 
  "Times New Roman")

;; fonts - otros
(define %bf-size%
  ;; Defines the body font size
  (case %visual-acuity%
    (("tiny") 8pt)
    (("normal") 12pt)
    (("presbyopic") 14pt)
    (("large-type") 32pt)))

;; Margen superior
(define %top-margin% 
  ;; Height of top margin
  (if (equal? %visual-acuity% "large-type")
      9.5pi
      8pi))

;; Margen inferior
(define %bottom-margin% 
  ;; Height of bottom margin
  (if (equal? %visual-acuity% "large-type")
      9.5pi 
      8pi))

;; Espaciamiento de línea
(define %line-spacing-factor% 
  ;; Factor used to calculate leading
  1.2)

;; Separación entre párrafos
(define %para-sep% 
  ;; Distance between paragraphs
  (/ %bf-size% 1.5))

;; Separación entre bloques
(define %block-sep% 
  ;; Distance between block-elements
  (* %para-sep% 1.1))


;; Título en página separada
(define %generate-article-titlepage-on-separate-page% 
  #t)

;; El footer del ISEIT es el mismo en ambos lados
(define %two-side% 
  #t)

;; -------------- Portada General ------------
(define (article-titlepage-recto-elements)
  (list 
   (normalize "mediaobject")
   (normalize "title")
   (normalize "subtitle")
   (normalize "corpauthor")
   (normalize "authorgroup")
   (normalize "author")
   (normalize "releaseinfo")
   (normalize "copyright")
   (normalize "pubdate")
   (normalize "revhistory")
   (normalize "abstract")))
;; Subtitulo centrado
(define %article-subtitle-quadding% 
  'center)


;; --------- Header de la primera página -----------
(define (first-page-inner-header gi)
  (literal "Copyright(C) 2006 - Alejandro Imass & Corvus Latinoamérica S.A."))
;; esto debería hacerse formalmente seleccionando
;; el copyright notice como tal - sorry segunda version
(define (first-page-center-header gi)
  (empty-sosofo))
(define (first-page-outer-header gi)
  ($title-header-footer$))
;; --------- Footers de las demás páginas -----------
(define (first-page-inner-footer gi)
  (literal "dotProject Unitcost Module Documentation"))
(define (first-page-center-footer gi)
  (empty-sosofo))
(define (first-page-outer-footer gi)
  (make sequence
    (literal "    Página:  ")
    ($page-number-header-footer$)))


;; --------- Headers de las demás páginas -----------
(define (page-inner-header gi)
  (literal "Copyright(C) 2006 - Alejandro Imass & Corvus Latinoamérica S.A."))
;; esto debería hacerse formalmente seleccionando
;; el copyright notice como tal - sorry segunda version
(define (page-center-header gi)
  (empty-sosofo))
(define (page-outer-header gi)
  ($title-header-footer$))
;; --------- Footers de las demás páginas -----------
(define (page-inner-footer gi)
  (literal "dotProject Unitcost Module Documentation"))
(define (page-center-footer gi)
  (empty-sosofo))
(define (page-outer-footer gi)
  (make sequence
    (literal "    Página:  ")
    ($page-number-header-footer$)))


;; Separa las láminas con 'rules'
(define %informalexample-rules%
  #t)
(define %object-rule-thickness%
  5pt)

;; las notas al pie van en la parte de abajo
(define bop-footnotes
  #t)

;; usar adminitions gráficos!
(define %admon-graphics%
  #f)

;; Que debe ir en el TOC
; (define ($generate-book-lot-list$)

;   (list 
;    (normalize "sect1") 
;    (normalize "sect2") 
;    ))


(define %generate-part-toc% 
  ;; Should a Table of Contents be produced for Parts?
  #t)

</style-specification-body>
</style-specification>
<external-specification id="docbook" document="dbstyle">
</style-sheet>


