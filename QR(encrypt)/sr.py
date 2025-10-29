import re
import nltk
from nltk.tokenize import word_tokenize, sent_tokenize

nltk.download('punkt')  # Ensure NLTK data is available

def extract_fields_from_ocr(ocr_result):
    """Extract name, address, and contact details from OCR text."""
    
    # Tokenize the OCR result into sentences
    sentences = sent_tokenize(ocr_result)

    # Predefined patterns for extracting information
    name_pattern = re.compile(r"(?i)(name|full name|nme|mr|ms|mrs)[\s:.]*([A-Za-z\s]+)")
    address_pattern = re.compile(r"(?i)(address|residence|location)[\s:.]*([\w\s,.-]+)")
    contact_pattern = re.compile(r"(?i)(contact|phone|mobile|tel)[\s:.]*([\d\s+()-]+)")

    name, address, contact = None, None, None

    # Iterate through sentences and apply regex patterns
    for sentence in sentences:
        if not name:
            match = name_pattern.search(sentence)
            if match:
                name = match.group(2).strip()

        if not address:
            match = address_pattern.search(sentence)
            if match:
                address = match.group(2).strip()

        if not contact:
            match = contact_pattern.search(sentence)
            if match:
                contact = match.group(2).strip()

    # Provide default values if fields are missing
    return {
        "name": name if name else "Unknown",
        "address": address if address else "Unknown",
        "contact": contact if contact else "Unknown"
    }

# Get input from user
ocr_text = input("Enter OCR-extracted text: ")
result = extract_fields_from_ocr(ocr_text)

# Display extracted details
print("\nExtracted Details:")
print("Name:", result["name"])
print("Address:", result["address"])
print("Contact:", result["contact"])
