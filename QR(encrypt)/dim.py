import re
import PyPDF2
import json
import sys

def extract_text_from_pdf(file_path, password=None):
    try:
        with open(file_path, 'rb') as file:
            pdf_reader = PyPDF2.PdfReader(file)
            if pdf_reader.is_encrypted:
                if password:
                    pdf_reader.decrypt(password)
                else:
                    return "The PDF is password-protected. Please provide the password."
            text = ""
            for page in pdf_reader.pages:
                text += page.extract_text()
            return text
    except Exception as e:
        return f"An error occurred: {e}"

def extract_details_based_on_to(text):
    details = {}

    # Find the word "TO" and extract the next line as the name
    lines = text.splitlines()
    name = "Not Found"
    for i, line in enumerate(lines):
        if "TO" in line.upper():  # Case-insensitive search for "TO"
            if i + 1 < len(lines):  # Ensure there is a next line
                name = lines[i + 2].strip()  # Adjust as needed for correct offset
            break

    # Regex patterns for other details
    aadhaar_match = re.search(r'\b\d{4}\s\d{4}\s\d{4}\b', text)
    phone_match = re.search(r'\b\d{10}\b', text)

    # Gender extraction (look for patterns like "Gender: Male" or "Gender: Female")
    gender_match = None
    gender_patterns = [r'\bGender\s*[:\-]?\s*(Male|Female)\b', r'\b(Male|Female)\b']
    for pattern in gender_patterns:
        gender_match = re.search(pattern, text, re.IGNORECASE)
        if gender_match:
            gender_match = gender_match.group(1)  # Extract the gender
            break
    
    # DOB extraction
    dob_match = re.search(r'\bDOB[:\s]*\d{2}/\d{2}/\d{4}\b', text)
    dob = dob_match.group().split(":")[-1].strip() if dob_match else "Not Found"

    # Storing matches or "Not Found"
    details['Name'] = name
    details['Aadhaar_Number'] = aadhaar_match.group() if aadhaar_match else "Not Found"
    details['Phone'] = phone_match.group() if phone_match else "Not Found"
    details['Gender'] = gender_match if gender_match else "Not Found"
    details['DOB'] = dob

    return details

# Main program starts here
if __name__ == "__main__":
    # Check if the correct number of arguments is passed
    if len(sys.argv) != 3:
        print(json.dumps({"error": "Please provide the correct number of arguments: <PDF_File_Path> <Password>."}))
    else:
        pdf_file = sys.argv[1]  # First argument: PDF file path
        password = sys.argv[2]  # Second argument: Password

        # Extract text from PDF
        extracted_text = extract_text_from_pdf(pdf_file, password)

        if extracted_text.startswith("An error occurred"):
            print(json.dumps({"error": extracted_text}))  # Return error in JSON format
        else:
            extracted_details = extract_details_based_on_to(extracted_text)
            
            # Print extracted details as JSON
            print(json.dumps(extracted_details))  # Return extracted details in JSON format
