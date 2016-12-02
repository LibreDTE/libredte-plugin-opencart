extension:
	zip libredte-opencart.ocmod.zip -r * > /dev/null

all: extension

clean:
	rm -f libredte-opencart.ocmod.zip
