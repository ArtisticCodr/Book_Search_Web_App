class Prefix(object):
    oznaka = ""
    labela = ""
    polje = ""
    podpolje = ""
    
    def eq(self, polje, podpolje):
        return polje == self.polje and podpolje == self.podpolje
        