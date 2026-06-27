import os
import re

def count_words(text):
    # Remove markdown formatting to get a cleaner word count
    clean_text = re.sub(r'[#*`_\-\[\]\(\)]', ' ', text)
    # Split by whitespace
    words = clean_text.split()
    return len(words)

def assemble():
    chapters_dir = r"c:\wamp64\www\finalyearproject\docs\chapters"
    output_file = r"c:\wamp64\www\finalyearproject\docs\research_report.md"
    
    chapter_files = [
        "prelims.md",
        "chapter1.md",
        "chapter2.md",
        "chapter3.md",
        "chapter4.md",
        "chapter5.md",
        "chapter6.md",
        "references_appendices.md"
    ]
    
    total_words = 0
    compiled_content = []
    
    print("Assembling final research report...")
    print("-" * 50)
    
    for filename in chapter_files:
        filepath = os.path.join(chapters_dir, filename)
        if not os.path.exists(filepath):
            print(f"Warning: File {filename} not found at {filepath}")
            continue
            
        with open(filepath, "r", encoding="utf-8") as f:
            content = f.read()
            
        words = count_words(content)
        total_words += words
        print(f"Loaded {filename}: {words} words")
        compiled_content.append(content)
        compiled_content.append("\n\n---\n\n") # Page break / section separator
        
    if compiled_content:
        # Join content and write to the output file
        # Remove the trailing separator from the last chapter
        final_content = "".join(compiled_content[:-1])
        
        with open(output_file, "w", encoding="utf-8") as f:
            f.write(final_content)
            
        print("-" * 50)
        print(f"Assembly complete! Written to: {output_file}")
        print(f"Total compiled word count: {total_words} words")
    else:
        print("Error: No chapter files found to assemble.")

if __name__ == "__main__":
    assemble()
