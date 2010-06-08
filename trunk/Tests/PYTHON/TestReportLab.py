from reportlab.pdfgen import canvas
from reportlab.lib.pagesizes import A4
from reportlab.lib.units import mm
#I'll be generating code39 barcodes, others are available
from reportlab.graphics.barcode import code39

# generate a canvas (A4 in this case, size doesn't really matter)
c=canvas.Canvas("c:/_tmp/barcode_example.pdf",pagesize=A4)

# create a barcode object
# (is not displayed yet)
# The encode text is '123456789'
# barHeight encodes how high the bars will be
# barWidth encodes how wide the "narrowest" barcode unit is
barcode=code39.Extended39("123456789",barWidth=0.5*mm,barHeight=20*mm)
# drawOn puts the barcode on the canvas at the specified coordinates
barcode.drawOn(c,100*mm,100*mm)

# now create the actual PDF
c.showPage()
c.save()
